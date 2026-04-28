<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMidwifeController;
use App\Http\Controllers\AdminParentController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ImmunizationController;
use App\Http\Controllers\ImmunizationStatusController;
use App\Http\Controllers\MidwifeController;
use App\Http\Controllers\MidwifeProfileController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ParentProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (GUEST LANDING)
|--------------------------------------------------------------------------
*/
Route::view('/', 'pages.home.guest');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::post('/midtrans/notification', [PaymentController::class, 'notification'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('midtrans.notification');

Route::get('/email/activated', function () {
    return view('auth.verified-success');
})->name('verification.activated');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('verification.activated');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi baru sudah dikirim ke email Anda.');
    })->middleware('throttle:6,1')->name('verification.send');
});
/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PARENT (ORANG TUA)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:parent', 'verified'])->group(function () {
        Route::get('/parent/dashboard', [ParentController::class, 'dashboard']);
        Route::get('/services', [ServiceController::class, 'index']);
        Route::get('/bookings', [BookingController::class, 'index']);

        // BOOKING
        Route::get('/booking/{service}', [BookingController::class, 'create']);
        Route::post('/booking/store', [BookingController::class, 'store']);
        Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

        // PAYMENT
        Route::get('/checkout/{booking}', [PaymentController::class, 'checkout'])->name('checkout');
        Route::post('/pay/{booking}', [PaymentController::class, 'pay']);
        Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
        Route::get('/payment/success/{transaction}', [PaymentController::class, 'success'])->name('payment.success');
        Route::post('/payment/recheck/{transaction}', [PaymentController::class, 'recheck'])->name('payment.recheck');

        Route::get('/child/{id}/status', [ImmunizationStatusController::class, 'show']);
        Route::post('/child/{id}/status', [ImmunizationStatusController::class, 'store']);
        Route::get('/children', [ChildController::class, 'index']);
        Route::get('/children/create', [ChildController::class, 'create']);
        Route::post('/children/store', [ChildController::class, 'store']);
        Route::get('/children/{id}/edit', [ChildController::class, 'edit']);
        Route::put('/children/{id}', [ChildController::class, 'update']);
        Route::delete('/children/{id}', [ChildController::class, 'destroy']);
        Route::get('/parent/profile', [ParentProfileController::class, 'show']);
        Route::post('/parent/profile', [ParentProfileController::class, 'update']);
        Route::post('/parent/profile/password', [ParentProfileController::class, 'updatePassword']);

    });
    /*
    |--------------------------------------------------------------------------
    | MIDWIFE (BIDAN)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:midwife'])->group(function () {

        Route::get('/dashboard', [MidwifeController::class, 'dashboard']);
        Route::get('/profile', [MidwifeProfileController::class, 'show']);
        Route::post('/profile', [MidwifeProfileController::class, 'update']);

        Route::get('/calendar', [MidwifeController::class, 'calendar']);
        Route::post('/midwife/bookings/{booking}/respond', [MidwifeController::class, 'respond']);

        Route::prefix('immunization')->group(function () {
            Route::get('/', [ImmunizationController::class, 'index']);
            Route::get('/create', [ImmunizationController::class, 'create']);
            Route::post('/store', [ImmunizationController::class, 'store']);
            Route::post('/bookings/{booking}/reschedule', [ImmunizationController::class, 'reschedule']);
        });

    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN (PUSKESMAS)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index']);
        Route::get('/admin/parents', [AdminParentController::class, 'index']);
        Route::post('/admin/parents/{parent}/reset-password', [AdminParentController::class, 'resetPassword']);
        Route::delete('/admin/parents/{parent}', [AdminParentController::class, 'destroy']);

        Route::get('/admin/services', [AdminServiceController::class, 'index']);
        Route::get('/admin/services/create', [AdminServiceController::class, 'create']);
        Route::post('/admin/services', [AdminServiceController::class, 'store']);
        Route::get('/admin/services/{service}/edit', [AdminServiceController::class, 'edit']);
        Route::put('/admin/services/{service}', [AdminServiceController::class, 'update']);
        Route::delete('/admin/services/{service}', [AdminServiceController::class, 'destroy']);

        Route::get('/admin/midwives', [AdminMidwifeController::class, 'index']);
        Route::get('/admin/midwives/schedules', [AdminMidwifeController::class, 'schedules']);
        Route::get('/admin/midwives/create', [AdminMidwifeController::class, 'create']);
        Route::post('/admin/midwives', [AdminMidwifeController::class, 'store']);
        Route::get('/admin/midwives/{midwife}/edit', [AdminMidwifeController::class, 'edit']);
        Route::put('/admin/midwives/{midwife}', [AdminMidwifeController::class, 'update']);
        Route::delete('/admin/midwives/{midwife}', [AdminMidwifeController::class, 'destroy']);

    });

});
