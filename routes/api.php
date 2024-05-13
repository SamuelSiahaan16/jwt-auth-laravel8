<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware'=>'api','prefix'=>'auth'], function($router){
    Route::post('/login', [AuthController::class, 'login'])->name('login');  
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile'); 
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.token'); 
});