<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Create a route to show cookies that are set in the browser
Route::get('/cookies', function (Request $request) {
    return response()->json($request->cookies->all());
});

/**
 * Auth routes
 */
Route::group(['prefix' => 'auth'], function () {
    /** @api {post} /auth/login Login a user */
    Route::post('/login', [AuthController::class, 'login']);

    /** @api {post} /auth/register Register a new user */
    Route::post('/register', [AuthController::class, 'register']);

    /** @api {post} /auth/verify-email Verify user email */
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');

    /** @api {post} /auth/resend-verification-email Resend verification email */
    Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail']);

    /** @api {post} /auth/consume-token Consume a token */
    Route::post('/consume-token', [AuthController::class, 'consumeToken']);

    /** @api {post} /auth/logout Logout a user */
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    /** @api {post} /auth/logout/all Logout all devices */
    Route::post('/logout/all', [AuthController::class, 'logoutAll'])->middleware('auth:sanctum');

    /** @api {get} /auth/user Get authenticated user */
    Route::get('/user', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:sanctum');

    /** @api {post} /auth/forgot-password Request a password reset link */
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    /** @api {post} /auth/reset-password Reset user password */
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

/**
 * Onboarding routes
 */
Route::group(['prefix' => 'onboarding'], function () {
    /** @api {get} /onboarding/current-stage Get current onboarding stage */
    Route::get('/current-stage', [OnboardingController::class, 'getCurrentStage'])->middleware('auth:sanctum');

    /** @api {get} /onboarding/status Get onboarding status */
    Route::get('/current-status', [OnboardingController::class, 'getOnboardingStatus'])->middleware('auth:sanctum');

    /** @api {post} /onboarding/update-stage Update onboarding stage */
    Route::post('/update-stage', [OnboardingController::class, 'updateOnboardingStage'])->middleware('auth:sanctum');

    /** @api {post} /onboarding/update-status Update onboarding status */
    Route::post('/update-status', [OnboardingController::class, 'updateOnboardingStatus'])->middleware('auth:sanctum');

    /** @api {get} /onboarding/stage/:stage Get onboarding stage */
    Route::get('/stage/{stage}', [OnboardingController::class, 'index'])->middleware('auth:sanctum');

    /** @api {get} /onboarding/next-stage Get next onboarding stage */
    Route::get('/next-stage', [OnboardingController::class, 'getNextStage'])->middleware('auth:sanctum');

    /** @api {get} /onboarding/previous-stage Get previous onboarding stage */
    Route::get('/previous-stage', [OnboardingController::class, 'getPreviousStage'])->middleware('auth:sanctum');

    /** @api {post} /onboarding/move-to-previous-stage Move to previous onboarding stage */
    Route::post('/move-to-previous-stage', [OnboardingController::class, 'moveToPreviousStage'])->middleware('auth:sanctum');

    /** @api {post} /onboarding/move-to-next-stage Move to next onboarding stage */
    Route::post('/move-to-next-stage', [OnboardingController::class, 'moveToNextStage'])->middleware('auth:sanctum');

    /** @api {post} /onboarding/welcome Welcome a user */
    Route::post('/welcome', [OnboardingController::class, 'welcome'])->middleware('auth:sanctum');

    /** @api {post} /onboarding/profile Complete profile onboarding */
    Route::post('/profile', [OnboardingController::class, 'profile'])->middleware('auth:sanctum');

    /** @api {post} /onboarding/company Complete company onboarding */
    Route::post('/company', [OnboardingController::class, 'company'])->middleware('auth:sanctum');

    /** @api {post} /onboarding/complete Complete onboarding */
    Route::post('/complete', [OnboardingController::class, 'complete'])->middleware('auth:sanctum');
});

/**
 * Board routes
 */
Route::group(['prefix' => 'boards'], function(){
    /** @api {get} /boards Get all boards */
    Route::get('/', [BoardController::class, 'index']);

    /** @api {get} /boards/find/by/user Get boards by user ID */
    Route::get('/find/by/user/{userId}', [BoardController::class, 'getBoardsByUserId']);

    /** @api {get} /boards/find/by/company Get boards by company ID */
    Route::get('/find/by/company/{companyId}', [BoardController::class, 'getBoardsByCompanyId']);

    /** @api {get} /boards/:id Get a board by ID */
    Route::get('/{id}', [BoardController::class, 'show']);

    /** @api {get} /boards/find/by/slug Get a board by slug */
    Route::get('/find/by/slug/{slug}', [BoardController::class, 'showBySlug']);

    /** @api {post} /boards/create Create a new board */
    Route::post('/create', [BoardController::class, 'store'])->middleware('auth:sanctum');

    /** @api {put} /boards/update/:id Update an existing board */
    Route::put('/update/{id}', [BoardController::class, 'update'])->middleware('auth:sanctum');

    /** @api {delete} /boards/delete/:id Delete a board */
    Route::delete('/delete/{id}', [BoardController::class, 'destroy'])->middleware('auth:sanctum');
});