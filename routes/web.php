<?php

use App\Http\Controllers\Web\Admin\AdminUserController;
use App\Http\Controllers\Web\Admin\AppVersionController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\InstituteController;
use App\Http\Controllers\Web\Admin\SettingController;
use App\Http\Controllers\Web\Admin\StudentController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\FirebaseServiceWorkerController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\Portal\PushTokenController;
use App\Http\Controllers\Web\Portal\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/firebase-messaging-sw.js', FirebaseServiceWorkerController::class)->name('firebase.sw');

Route::get('/privacy-policy', function () {
    return view('privacy_policy');
})->name('privacy.policy');

/*
| One login page: email = hub admin, portal username = student / parent
*/
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt')->middleware('throttle:30,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
| Hub admin
*/
Route::prefix('admin')->name('admin.')->middleware('hub.admin')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/institutes', [InstituteController::class, 'index'])->name('institutes.index');
    Route::get('/institutes/{institute}', [InstituteController::class, 'show'])->name('institutes.show');
    Route::put('/institutes/{institute}', [InstituteController::class, 'update'])->name('institutes.update');
    Route::patch('/institutes/{institute}/toggle', [InstituteController::class, 'toggle'])->name('institutes.toggle');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::patch('/students/{student}/toggle', [StudentController::class, 'toggle'])->name('students.toggle');

    Route::get('/app-versions', [AppVersionController::class, 'index'])->name('app-versions.index');
    Route::post('/app-versions', [AppVersionController::class, 'store'])->name('app-versions.store');
    Route::patch('/app-versions/{appVersion}', [AppVersionController::class, 'update'])->name('app-versions.update');
    Route::delete('/app-versions/{appVersion}', [AppVersionController::class, 'destroy'])->name('app-versions.destroy');

    Route::get('/admins', [AdminUserController::class, 'index'])->name('admins.index');
    Route::post('/admins', [AdminUserController::class, 'store'])->name('admins.store');
    Route::patch('/admins/{admin}/toggle', [AdminUserController::class, 'toggle'])->name('admins.toggle');
    Route::put('/password', [AdminUserController::class, 'password'])->name('password');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

/*
| Student / parent portal
*/
Route::prefix('portal')->name('portal.')->middleware('portal')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [PortalController::class, 'attendance'])->name('attendance');
    Route::get('/payments', [PortalController::class, 'payments'])->name('payments');
    Route::get('/exams', [PortalController::class, 'exams'])->name('exams');
    Route::get('/exams/{exam}', [PortalController::class, 'result'])->whereNumber('exam')->name('result');
    Route::get('/schedule', [PortalController::class, 'schedule'])->name('schedule');
    Route::get('/notifications', [PortalController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [PortalController::class, 'readAllNotifications'])->name('notifications.read-all');
    Route::get('/profile', [PortalController::class, 'profile'])->name('profile');
    Route::put('/password', [PortalController::class, 'changePassword'])->name('password');
    Route::post('/push-token', [PushTokenController::class, 'store'])->middleware('throttle:10,1')->name('push-token');
});
