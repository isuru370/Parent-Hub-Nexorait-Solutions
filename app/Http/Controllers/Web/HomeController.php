<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\Portal\PortalSession;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(PortalSession $portal): View
    {
        // Where the "Log in" button should take someone who is already signed in.
        $account = match (true) {
            auth()->check() => ['url' => route('admin.dashboard'), 'label' => 'Admin dashboard'],
            $portal->check() => ['url' => route('portal.dashboard'), 'label' => 'My portal'],
            default => null,
        };

        return view('welcome', [
            'account' => $account,
            'androidUrl' => SystemSetting::get('android_app_url'),
            'iosUrl' => SystemSetting::get('ios_app_url'),
            'supportPhone' => SystemSetting::get('support_phone'),
            'supportEmail' => SystemSetting::get('support_email'),
        ]);
    }
}
