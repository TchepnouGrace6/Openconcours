<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Page d'accueil
Route::get('/home', function () {
    return view('home');
});

// Autres routes ici...

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
