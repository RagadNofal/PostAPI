<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
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
Route::group(['prefix' => 'post','middleware' => 'auth:sanctum'],function(){

    Route::get('all', [PostController::class, 'index']);
    Route::get('users/{userId}/posts', [PostController::class, 'getUserPosts']);
    Route::get('showPosts/{id}', [PostController::class, 'show']);
    Route::post('addPost', [PostController::class, 'store']);
    Route::put('EditePost/{id}', [PostController::class, 'update']);
    Route::delete('DeletePost/{id}', [PostController::class, 'destroy']);
});

Route::group(['prefix' => 'users'] , function () {
    Route::get('all' , [UserController::class , 'index']);
    Route::get('{id}/show' , [UserController::class , 'show']);
    Route::post('/' , [UserController::class , 'store']);
    Route::put('{id}/update' , [UserController::class , 'update']);
    Route::delete('{id}/delete' , [UserController::class , 'delete']);
});
