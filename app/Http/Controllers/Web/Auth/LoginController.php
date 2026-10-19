<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use App\Services\Portal\PortalSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * One login page for everyone:
 *  - an email address  -> Parent Hub admin
 *  - a portal username -> student / parent (checked by their institute)
 */
class LoginController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private PortalSession $portal
    ) {}

    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        if ($this->portal->check()) {
            return redirect()->route('portal.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'max:150'],
        ], [
            'login.required' => 'Enter your email or student username.',
        ]);

        $login = trim($credentials['login']);
        $throttleKey = Str::lower($login) . '|' . $request->ip();
        $maxAttempts = max(1, (int) config('hub.login_attempts_per_minute', 5));

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            throw ValidationException::withMessages([
                'login' => 'Too many attempts. Try again in ' . RateLimiter::availableIn($throttleKey) . ' seconds.',
            ]);
        }

        $redirect = Str::contains($login, '@')
            ? $this->adminLogin($request, $login, $credentials['password'])
            : $this->studentLogin($request, $login, $credentials['password']);

        if ($redirect === null) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'login' => session()->pull('login_error', 'Incorrect username or password.'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        return $redirect;
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $this->portal->forget();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }

    protected function adminLogin(Request $request, string $email, string $password): ?RedirectResponse
    {
        $user = User::query()->where('email', $email)->first();

        if ($user && !$user->is_active) {
            session()->flash('login_error', 'This admin account is disabled.');

            return null;
        }

        if (!Auth::attempt(['email' => $email, 'password' => $password, 'is_active' => true], $request->boolean('remember'))) {
            return null;
        }

        $this->portal->forget();
        $request->session()->regenerate();

        Auth::user()->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('admin.dashboard'));
    }

    protected function studentLogin(Request $request, string $username, string $password): ?RedirectResponse
    {
        $result = $this->authService->studentLogin($username, $password);

        if (!$result['status']) {
            session()->flash('login_error', $result['message']);

            return null;
        }

        Auth::guard('web')->logout();
        $this->portal->start($username, $result);

        return redirect()->route('portal.dashboard');
    }
}
