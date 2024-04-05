<?php

use App\Http\Controllers\Api\V1\Auth\Auth as AuthController;
use Illuminate\Support\Facades\Auth;

// use App\Http\Controllers\Api\V1\Auth\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController as AuthVerifyEmailController;
use App\Http\Controllers\APi\V1\Cases\Cases;
use App\Http\Controllers\Api\V1\Documents\Documents;
use App\Http\Controllers\Api\V1\Profiles\Profiles;
use App\Http\Controllers\Api\V1\Receive\Receive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/email/verify/{id}/{hash}', AuthVerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');


Route::get('/email/verify/success' , function(User $user){
    return response()->json([
        'status' => 200,
        'message' => 'email verfied'
    ]);
})->name('email.verified');


// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json([
        'status' => 200,
        'message' => 'email sent'
    ]);
    // return redirect()->back()->with('message', 'Verification link sent!');
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');


Route::middleware(['api'])->prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});


Route::middleware(['api' , 'jwtMiddleware'])->group(function(){
    Route::apiResource('profile' , Profiles::class);
    Route::apiResource('case' , Cases::class);

    Route::apiResource('receive' , Receive::class)->except('update' , 'delete');
    Route::apiResource('docs' , Documents::class);
});






