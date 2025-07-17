<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Auth routes
 */
Route::group(['prefix' => 'auth'], function () {
    /** @api {post} /auth/login Login a user */
    Route::post('/login', [AuthController::class, 'login']);

    /** @api {post} /auth/register Register a new user */
    Route::post('/register', [AuthController::class, 'register']);

    /** @api {post} /auth/logout Logout a user */
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    /** @api {post} /auth/logout/all Logout all devices */
    Route::post('/logout/all', [AuthController::class, 'logoutAll'])->middleware('auth:sanctum');

    /** @api {get} /auth/user Get authenticated user */
    Route::get('/user', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:sanctum');
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