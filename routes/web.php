<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EcoleController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\ConcoursController;
use App\Http\Controllers\ConcoursSessionController;
use App\Http\Controllers\EnrollementController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\CentreExamenConcoursController;
use App\Http\Controllers\CentreDepotController;

// Page d'accueil
Route::get('/home', function () {
    return view('home');
});

// Autres routes ici...


Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');