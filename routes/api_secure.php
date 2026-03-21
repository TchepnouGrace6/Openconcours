<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CentreDepotController;
use App\Http\Controllers\CentreExamenConcoursController;
use App\Http\Controllers\CentreExamenController;
use App\Http\Controllers\ConcoursController;
use App\Http\Controllers\ConcoursSessionController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\EcoleController;
use App\Http\Controllers\EnrollementController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

// ========================================
// ROUTES PUBLIQUES (sans authentification)
// ========================================

// Authentification
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// ========================================
// ROUTES PROTÉGÉES - CANDIDATS ET ADMINS
// ========================================

Route::middleware('auth:sanctum')->group(function () {

    // LECTURE SEULE - Accessible aux candidats et admins
    Route::get('ecoles', [EcoleController::class, 'index']);
    Route::get('ecoles/{id}', [EcoleController::class, 'show']);
    Route::get('departements', [DepartementController::class, 'index']);
    Route::get('departements/{id}', [DepartementController::class, 'show']);
    Route::get('filieres', [FiliereController::class, 'index']);
    Route::get('filieres/{id}', [FiliereController::class, 'show']);
    Route::get('niveaux', [NiveauController::class, 'index']);
    Route::get('niveaux/{id}', [NiveauController::class, 'show']);
    Route::get('concours', [ConcoursController::class, 'index']);
    Route::get('concours/{id}', [ConcoursController::class, 'show']);
    Route::get('sessions', [ConcoursSessionController::class, 'index']);
    Route::get('sessions/{id}', [ConcoursSessionController::class, 'show']);
    Route::get('centre-examen', [CentreExamenController::class, 'index']);
    Route::get('centre-examen/{id}', [CentreExamenController::class, 'show']);
    Route::get('centre-depot', [CentreDepotController::class, 'index']);
    Route::get('centre-depot/{id}', [CentreDepotController::class, 'show']);
    Route::get('salles', [SalleController::class, 'index']);
    Route::get('salles/{id}', [SalleController::class, 'show']);

    // ACTIONS CANDIDATS
    Route::post('enrollements', [EnrollementController::class, 'store']);
    Route::get('enrollements/{id}', [EnrollementController::class, 'show']);
    Route::get('enrollements/{id}/export-pdf', [EnrollementController::class, 'exportPdf']);
    Route::post('paiements', [PaiementController::class, 'store']);
    Route::get('mes-paiements', [PaiementController::class, 'mesPaiements']);
});

// ========================================
// ROUTES RÉSERVÉES AUX ADMINISTRATEURS
// ========================================

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // Création d'administrateurs
    Route::post('create-admin', [AuthController::class, 'createAdmin']);

    // Statistiques
    Route::get('stats', [StatsController::class, 'getStats']);
    Route::get('concours/actifs', [StatsController::class, 'getConcours']);
    Route::get('enrollements/pending-count', [EnrollementController::class, 'pendingCount']);

    // Gestion des écoles
    Route::post('ecoles', [EcoleController::class, 'store']);
    Route::put('ecoles/{id}', [EcoleController::class, 'update']);
    Route::delete('ecoles/{id}', [EcoleController::class, 'destroy']);

    // Gestion des départements
    Route::post('departements', [DepartementController::class, 'store']);
    Route::put('departements/{id}', [DepartementController::class, 'update']);
    Route::delete('departements/{id}', [DepartementController::class, 'destroy']);

    // Gestion des filières
    Route::post('filieres', [FiliereController::class, 'store']);
    Route::put('filieres/{id}', [FiliereController::class, 'update']);
    Route::delete('filieres/{id}', [FiliereController::class, 'destroy']);

    // Gestion des niveaux
    Route::post('niveaux', [NiveauController::class, 'store']);
    Route::put('niveaux/{id}', [NiveauController::class, 'update']);
    Route::delete('niveaux/{id}', [NiveauController::class, 'destroy']);

    // Gestion des concours
    Route::post('concours', [ConcoursController::class, 'store']);
    Route::put('concours/{id}', [ConcoursController::class, 'update']);
    Route::delete('concours/{id}', [ConcoursController::class, 'destroy']);

    // Gestion des sessions
    Route::post('sessions', [ConcoursSessionController::class, 'store']);
    Route::put('sessions/{id}', [ConcoursSessionController::class, 'update']);
    Route::delete('sessions/{id}', [ConcoursSessionController::class, 'destroy']);

    // Gestion des enrôlements (admin)
    Route::get('enrollements', [EnrollementController::class, 'index']);
    Route::put('enrollements/{id}', [EnrollementController::class, 'update']);
    Route::delete('enrollements/{id}', [EnrollementController::class, 'destroy']);

    // Gestion des centres d'examen
    Route::post('centre-examen', [CentreExamenController::class, 'store']);
    Route::put('centre-examen/{id}', [CentreExamenController::class, 'update']);
    Route::delete('centre-examen/{id}', [CentreExamenController::class, 'destroy']);

    // Gestion des centres de dépôt
    Route::post('centre-depot', [CentreDepotController::class, 'store']);
    Route::put('centre-depot/{id}', [CentreDepotController::class, 'update']);
    Route::delete('centre-depot/{id}', [CentreDepotController::class, 'destroy']);

    // Gestion des salles
    Route::post('salles', [SalleController::class, 'store']);
    Route::put('salles/{id}', [SalleController::class, 'update']);
    Route::delete('salles/{id}', [SalleController::class, 'destroy']);

    // Gestion des relations centre-examen-concours
    Route::get('centre-examen-concours', [CentreExamenConcoursController::class, 'index']);
    Route::get('centre-examen-concours/{id}', [CentreExamenConcoursController::class, 'show']);
    Route::post('centre-examen-concours', [CentreExamenConcoursController::class, 'store']);
    Route::put('centre-examen-concours/{id}', [CentreExamenConcoursController::class, 'update']);
    Route::delete('centre-examen-concours/{id}', [CentreExamenConcoursController::class, 'destroy']);

    // Gestion des paiements (lecture seule pour admin)
    Route::get('paiements', [PaiementController::class, 'index']);
});
