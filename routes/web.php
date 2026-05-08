<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/register', function () {
    return view('register.index');
});
Route::post('/register', [UserController::class, 'register']);

Route::get('/login', function () {
    return view('login.index');
});
Route::post('/login', [UserController::class, 'login']);
