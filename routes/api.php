<?php

use App\Http\Controllers\Api\V1\Auth\Auth as AuthController;
use App\Http\Controllers\Api\V1\Booking\BookingController;
use App\Http\Controllers\Api\V1\Cases\Cases;
use App\Http\Controllers\Api\V1\Documents\Documents;
use App\Http\Controllers\Api\V1\Papers\Papers;
use App\Http\Controllers\Api\V1\ProfilePaper\ProfilePaper;
use App\Http\Controllers\Api\V1\Profiles\Profiles;
use App\Http\Controllers\Api\V1\Receive\Receive;
use App\Http\Controllers\Api\V1\Verification\Verification;
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


Route::apiResource('profile' , Profiles::class)->only('index' , 'show');

Route::middleware(['api' , 'jwtMiddleware'])->group(function(){

    Route::prefix('auth')->group(function(){
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    Route::apiResource('profile' , Profiles::class)->except('index' , 'show');
    Route::get('check/profile' , [Profiles::class , 'userHaveProfile']);
    Route::apiResource('case' , Cases::class);
    Route::get('cases/detail' , [Cases::class , 'getCaseByToken']);
    Route::apiResource('receive' , Receive::class)->except('update');
    Route::apiResource('docs' , Documents::class);

    Route::apiResource('booking' , BookingController::class);

    Route::apiResource('verification' , Verification::class);

    /******************************** Papers Api *******************************/

    Route::get('papers' , [Papers::class , 'index']);
    Route::get('papers/detail' , [Papers::class , 'getPaperById']);
    Route::post('papers' , [Papers::class , 'createPaper']);
    Route::patch('papers' , [Papers::class , 'updatePaper']);
    Route::delete('papers' , [Papers::class , 'deletePaper']);

    /******************************** Profile Papers Api *******************************/

    Route::get('profile/papers/detail' , [ProfilePaper::class , 'getProfilePapersById']);
    Route::post('profile/papers' , [ProfilePaper::class , 'createProfilePapers']);
    Route::patch('profile/papers/update/status' , [ProfilePaper::class , 'updateProfilePaperstStatus']);
    Route::delete('profile/papers/delete' , [ProfilePaper::class , 'deleteProfilePapers']);

});






