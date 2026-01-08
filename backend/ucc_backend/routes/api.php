<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\ChatController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');;
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');;

Route::get('/events', [EventsController::class, 'getEvents']);

Route::post('/password/email', [AuthController::class, 'sendResetLink']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

});

Route::middleware('auth:api')->group(function () {
    Route::post('/event', [EventsController::class, 'store']);
    Route::put('/event/{id}', [EventsController::class, 'updateEvent']);
    Route::delete('/event/{id}', [EventsController::class, 'deleteEvent']);
});


Route::middleware('auth:api')->group(function () {
    Route::post('/chat', [ChatController::class, 'chat']);
});
