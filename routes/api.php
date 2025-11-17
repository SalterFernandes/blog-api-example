<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CategoryController;

//Public routes
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Posts (#Public)
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{idOrSlug}', [PostController::class, 'show']);
    Route::get('/users/{userId}/posts', [PostController::class, 'byUser']);
    Route::get('/categories/{categoryId}/posts', [PostController::class, 'byCategory']);

    // Categories (#Public)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Comments (#Public)
    Route::get('/posts/{postId}/comments', [CommentController::class, 'index']);

});

//Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function (){

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Posts
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // Categories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Comments
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
});
