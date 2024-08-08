<?php

use App\Http\Controllers\Api\V1\Auth\Auth as AuthController;
use App\Http\Controllers\Api\V1\Booking\BookingController;
use App\Http\Controllers\Api\V1\CaseProfileNotes\CaseProfileNotesController;
use App\Http\Controllers\Api\V1\Cases\Cases;
use App\Http\Controllers\Api\V1\Currency\CurrencyController;
use App\Http\Controllers\Api\V1\Documents\Documents;
use App\Http\Controllers\Api\V1\keywords\KeywordsController;
use App\Http\Controllers\Api\V1\Papers\Papers;
use App\Http\Controllers\Api\V1\ProfileEducation\ProfileEducationController;
use App\Http\Controllers\Api\V1\ProfilePaper\ProfilePaper;
use App\Http\Controllers\Api\V1\Profiles\Profiles;
use App\Http\Controllers\Api\V1\ProfileSocials\ProfileSocialsController;
use App\Http\Controllers\Api\V1\ProfileWorkExperience\ProfileWorkExperienceController;
use App\Http\Controllers\Api\V1\Receive\Receive;
use App\Http\Controllers\Api\V1\Skills\SkillsController;
use App\Http\Controllers\Api\V1\SocialMedia\SocialMediaController;
use App\Http\Controllers\Api\V1\Types\TypesController;
use App\Http\Controllers\Api\V1\Verification\Verification;
use App\Http\Controllers\Api\V1\WorkedCaseNotes\WorkedCaseNotesController;
use App\Http\Controllers\Api\V1\WorkedCases\WorkedCasesController;
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


Route::apiResource('profile', Profiles::class)->only('index', 'show');
Route::apiResource('type', TypesController::class)->only('index');
Route::apiResource('currency', CurrencyController::class)->only('index');
Route::apiResource('skills', SkillsController::class)->only('index');
Route::apiResource('keywords', KeywordsController::class)->only('index');

Route::middleware(['api', 'jwtMiddleware'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    Route::apiResource('profile' , Profiles::class)->except('index' , 'show');
    Route::get('get/profile/from/token' , [Profiles::class , 'getProfileFromToken']);
    Route::patch('update/profile/from-token' , [Profiles::class , 'updateProfileFromToken']);

    Route::post('profile/restore/{id}' , [Profiles::class , 'restore']);
    Route::get('user-profile' ,  [Profiles::class , 'getUserProfile']);

    Route::get('check/profile', [Profiles::class, 'userHaveProfile']);

    Route::apiResource('case', Cases::class);
    Route::get('cases/detail', [Cases::class, 'getCaseByToken']);
    Route::get('case/get/all', [Cases::class, 'getAllDataWithoutPaginate']);
    Route::get('case/trashed/all', [Cases::class, 'getAllTrashedData']);
    Route::post('case/logs/{id}', [Cases::class, 'getLogs']);
    Route::post('case/restore/{id}', [Cases::class, 'restore']);
    Route::get('cases/details', [Cases::class, 'getAllUserCasesWithApplyers']);

    Route::apiResource('receive', Receive::class);
    Route::get('receive/get/all', [Receive::class, 'getAllDataWithoutPaginate']);
    Route::get('receive/trashed/all', [Receive::class, 'getAllTrashedData']);
    Route::post('receive/logs/{id}', [Receive::class, 'getLogs']);
    Route::post('receive/restore/{id}', [Receive::class, 'restore']);

    // Route::apiResource('booking' , BookingController::class);
    Route::post('booking/create', [BookingController::class, 'create']);
    Route::post('booking/update', [BookingController::class, 'update']);
    Route::post('booking/change-status', [BookingController::class, 'changeStatus']);
    Route::get('booking/get-all-booked-by-requester', [BookingController::class, 'getAllUserBooking']);
    Route::get('booking/get-all-booking-by-receiver', [BookingController::class, 'getAllProfileBooking']);
    Route::get('booking/show', [BookingController::class, 'show']);
    Route::post('booking/delete', [BookingController::class, 'delete']);
    Route::get('booking/all-trashed-by-token', [BookingController::class, 'getAllTrashedDataByToken']);

    Route::apiResource('verification', Verification::class);
    Route::get('verification/get/all', [Verification::class, 'getAllDataWithoutPaginate']);
    Route::get('verification/trashed/all', [Verification::class, 'getAllTrashedData']);
    Route::post('verification/logs/{id}', [Verification::class, 'getLogs']);
    Route::post('verification/restore/{id}', [Verification::class, 'restore']);

    // Route::apiResource('type' , TypesController::class)->only('index');
    // Route::get('type/get/all' , [TypesController::class , 'getAllDataWithoutPaginate']);
    // Route::get('type/trashed/all', [TypesController::class, 'getAllTrashedData']);
    // Route::post('type/logs/{id}' , [TypesController::class , 'getLogs']);
    // Route::post('type/restore/{id}' , [TypesController::class , 'restore']);

    // Route::apiResource('currency' , CurrencyController::class)->only('index');
    // Route::get('currency/get/all' , [CurrencyController::class , 'getAllDataWithoutPaginate']);
    // Route::get('currency/trashed/all', [CurrencyController::class, 'getAllTrashedData']);
    // Route::post('currency/logs/{id}' , [CurrencyController::class , 'getLogs']);
    // Route::post('currency/restore/{id}' , [CurrencyController::class , 'restore']);

    // Route::apiResource('skills' , SkillsController::class)->only('index');
    // Route::get('skills/get/all' , [SkillsController::class , 'getAllDataWithoutPaginate']);
    // Route::get('skills/trashed/all', [SkillsController::class, 'getAllTrashedData']);
    // Route::post('skills/logs/{id}' , [SkillsController::class , 'getLogs']);
    // Route::post('skills/restore/{id}' , [SkillsController::class , 'restore']);

    // Route::apiResource('keywords' , KeywordsController::class)->only('index');
    // Route::get('keywords/get/all' , [KeywordsController::class , 'getAllDataWithoutPaginate']);
    // Route::get('keywords/trashed/all', [KeywordsController::class, 'getAllTrashedData']);
    // Route::post('keywords/logs/{id}' , [KeywordsController::class , 'getLogs']);
    // Route::post('keywords/restore/{id}' , [KeywordsController::class , 'restore']);

    Route::apiResource('case/profile/note', CaseProfileNotesController::class);
    Route::get('case/profile/note/get/all', [CaseProfileNotesController::class, 'getAllDataWithoutPaginate']);
    Route::get('case/profile/note/trashed/all', [CaseProfileNotesController::class, 'getAllTrashedData']);
    Route::post('case/profile/note/logs/{id}', [CaseProfileNotesController::class, 'getLogs']);
    Route::post('case/profile/note/restore/{id}', [CaseProfileNotesController::class, 'restore']);

    Route::apiResource('worked/cases', WorkedCasesController::class);
    Route::get('worked/cases/get/all', [WorkedCasesController::class, 'getAllDataWithoutPaginate']);
    Route::get('worked/cases/trashed/all', [WorkedCasesController::class, 'getAllTrashedData']);
    Route::post('worked/cases/logs/{id}', [WorkedCasesController::class, 'getLogs']);
    Route::post('worked/cases/restore/{id}', [WorkedCasesController::class, 'restore']);

    Route::apiResource('worked/case-notes', WorkedCaseNotesController::class);
    Route::get('worked/case-notes/get/all', [WorkedCaseNotesController::class, 'getAllDataWithoutPaginate']);
    Route::get('worked/case-notes/trashed/all', [WorkedCaseNotesController::class, 'getAllTrashedData']);
    Route::post('worked/case-notes/logs/{id}', [WorkedCaseNotesController::class, 'getLogs']);
    Route::post('worked/case-notes/restore/{id}', [WorkedCaseNotesController::class, 'restore']);

    Route::apiResource('social', SocialMediaController::class);
    Route::get('social/get/all', [SocialMediaController::class, 'getAllDataWithoutPaginate']);
    Route::get('social/trashed/all', [SocialMediaController::class, 'getAllTrashedData']);
    Route::post('social/logs/{id}', [SocialMediaController::class, 'getLogs']);
    Route::post('social/restore/{id}', [SocialMediaController::class, 'restore']);


    Route::apiResource('profile-social', ProfileSocialsController::class);
    Route::get('profile-social/get/all', [ProfileSocialsController::class, 'getAllDataWithoutPaginate']);
    Route::get('profile-social/trashed/all', [ProfileSocialsController::class, 'getAllTrashedData']);
    Route::post('profile-social/logs/{id}', [ProfileSocialsController::class, 'getLogs']);
    Route::post('profile-social/restore/{id}', [ProfileSocialsController::class, 'restore']);
    Route::patch('profile-social/update/all',[ProfileSocialsController::class,'updateProfileSocialsArrayByProfileId']);

    Route::apiResource('profile-education', ProfileEducationController::class);
    Route::get('profile-education/get/all', [ProfileEducationController::class, 'getAllDataWithoutPaginate']);
    Route::get('profile-education/trashed/all', [ProfileEducationController::class, 'getAllTrashedData']);
    Route::post('profile-education/logs/{id}', [ProfileEducationController::class, 'getLogs']);
    Route::post('profile-education/restore/{id}', [ProfileEducationController::class, 'restore']);

    Route::apiResource('profile-work-experience', ProfileWorkExperienceController::class);
    Route::get('profile-work-experience/get/all', [ProfileWorkExperienceController::class, 'getAllDataWithoutPaginate']);
    Route::get('profile-work-experience/trashed/all', [ProfileWorkExperienceController::class, 'getAllTrashedData']);
    Route::post('profile-work-experience/logs/{id}', [ProfileWorkExperienceController::class, 'getLogs']);
    Route::post('profile-work-experience/restore/{id}', [ProfileWorkExperienceController::class, 'restore']);

    /******************************** Papers Api *******************************/

    Route::get('papers', [Papers::class, 'index']);
    Route::get('papers/detail', [Papers::class, 'getPaperById']);
    Route::post('papers', [Papers::class, 'createPaper']);
    Route::patch('papers', [Papers::class, 'updatePaper']);
    Route::delete('papers', [Papers::class, 'deletePaper']);

    /******************************** Profile Papers Api *******************************/

    Route::get('profile/papers/detail', [ProfilePaper::class, 'getProfilePapersById']);
    Route::post('profile/papers', [ProfilePaper::class, 'createProfilePapers']);
    Route::patch('profile/papers/update/status', [ProfilePaper::class, 'updateProfilePaperstStatus']);
    Route::delete('profile/papers/delete', [ProfilePaper::class, 'deleteProfilePapers']);
});


Route::middleware('internalToken')->group(function () {
    Route::apiResource('type', TypesController::class)->except('index');
    Route::get('type/get/all', [TypesController::class, 'getAllDataWithoutPaginate']);
    Route::get('type/trashed/all', [TypesController::class, 'getAllTrashedData']);
    Route::post('type/logs/{id}', [TypesController::class, 'getLogs']);
    Route::post('type/restore/{id}', [TypesController::class, 'restore']);

    Route::apiResource('currency', CurrencyController::class)->except('index');
    Route::get('currency/get/all', [CurrencyController::class, 'getAllDataWithoutPaginate']);
    Route::get('currency/trashed/all', [CurrencyController::class, 'getAllTrashedData']);
    Route::post('currency/logs/{id}', [CurrencyController::class, 'getLogs']);
    Route::post('currency/restore/{id}', [CurrencyController::class, 'restore']);


    Route::apiResource('skills', SkillsController::class)->except('index');
    Route::get('skills/get/all', [SkillsController::class, 'getAllDataWithoutPaginate']);
    Route::get('skills/trashed/all', [SkillsController::class, 'getAllTrashedData']);
    Route::post('skills/logs/{id}', [SkillsController::class, 'getLogs']);
    Route::post('skills/restore/{id}', [SkillsController::class, 'restore']);


    Route::apiResource('keywords', KeywordsController::class)->except('index');
    Route::get('keywords/get/all', [KeywordsController::class, 'getAllDataWithoutPaginate']);
    Route::get('keywords/trashed/all', [KeywordsController::class, 'getAllTrashedData']);
    Route::post('keywords/logs/{id}', [KeywordsController::class, 'getLogs']);
    Route::post('keywords/restore/{id}', [KeywordsController::class, 'restore']);

    Route::get('booking/get/all', [BookingController::class, 'index']);
});
