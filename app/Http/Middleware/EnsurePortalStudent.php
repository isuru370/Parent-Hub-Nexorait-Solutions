<?php

namespace App\Http\Middleware;

use App\Services\Portal\PortalSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Student / parent portal: a student logged in through their institute.
 */
class EnsurePortalStudent
{
    public function __construct(private PortalSession $portal)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->portal->check()) {
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Please log in again.'], 401);
            }

            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
