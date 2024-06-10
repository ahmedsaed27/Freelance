<?php

use App\Http\Controllers\Api\V1\Auth\Auth as AuthController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController as AuthVerifyEmailController;
use App\Http\Controllers\APi\V1\Cases\Cases;
use App\Http\Controllers\Api\V1\Documents\Documents;
use App\Http\Controllers\Api\V1\Profiles\Profiles;
use App\Http\Controllers\Api\V1\Receive\Receive;
use App\Http\Controllers\Api\V1\SavedJobs\SavedJobs;
use App\Http\Controllers\Api\V1\Verification\Verification;
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


Route::middleware(['api'])->prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});


Route::middleware(['api' , 'jwtMiddleware'])->group(function(){

    Route::prefix('auth')->group(function(){
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    Route::apiResource('profile' , Profiles::class);
    Route::apiResource('case' , Cases::class);
    Route::apiResource('receive' , Receive::class)->except('update');
    Route::apiResource('docs' , Documents::class);

    // Route::apiResource('jobs/saved' , SavedJobs::class)->except('update');
    Route::apiResource('verification' , Verification::class);

});






