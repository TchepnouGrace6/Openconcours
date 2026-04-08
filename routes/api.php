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

// Iscription des candidats et création des admins
// Connexion (login)
Route::get('ecoles', [EcoleController::class, 'index']);
Route::post('login', [AuthController::class, 'login']);

Route::post('register', [AuthController::class, 'register']);
// inscription candidat
Route::middleware('auth:sanctum')->post('create-admin', [AuthController::class, 'createAdmin']); // création admin
Route::middleware('auth:sanctum')->get('enrollements/pending-count', [EnrollementController::class, 'pendingCount']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('ecoles', [EcoleController::class, 'store']);
    Route::put('ecoles/{id}', [EcoleController::class, 'update']);
    Route::delete('ecoles/{id}', [EcoleController::class, 'destroy']);

    Route::get('ecoles/{id}', [EcoleController::class, 'show']);
});

Route::get('/stats', [StatsController::class, 'getStats']);
Route::get('/concours/actifs', [StatsController::class, 'getConcours']);

// Routes accessibles à tous les utilisateurs connectés
Route::middleware('auth:sanctum')->group(function () {
    Route::get('departements', [DepartementController::class, 'index']);
    Route::get('departements/{id}', [DepartementController::class, 'show']);
    Route::post('departements', [DepartementController::class, 'store']);
    Route::put('departements/{id}', [DepartementController::class, 'update']);
    Route::delete('departements/{id}', [DepartementController::class, 'destroy']);
});

// Routes accessibles à tous les utilisateurs connectés (candidats et admins)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('filieres', [FiliereController::class, 'index']);
    Route::get('filieres/{id}', [FiliereController::class, 'show']);

    Route::post('filieres', [FiliereController::class, 'store']);
    Route::put('filieres/{id}', [FiliereController::class, 'update']);
    Route::delete('filieres/{id}', [FiliereController::class, 'destroy']);
});

// Routes accessibles à tous les utilisateurs connectés (candidats et admins)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('niveaux', [NiveauController::class, 'index']);
    Route::get('niveaux/{id}', [NiveauController::class, 'show']);

    Route::post('niveaux', [NiveauController::class, 'store']);
    Route::put('niveaux/{id}', [NiveauController::class, 'update']);
    Route::delete('niveaux/{id}', [NiveauController::class, 'destroy']);
});

// Routes accessibles aux candidats et admins
Route::middleware('auth:sanctum')->group(function () {
    Route::get('concours', [ConcoursController::class, 'index']);
    Route::get('concours/{id}', [ConcoursController::class, 'show']);

    Route::post('concours', [ConcoursController::class, 'store']);
    Route::put('concours/{id}', [ConcoursController::class, 'update']);
    Route::delete('concours/{id}', [ConcoursController::class, 'destroy']);
});

// Routes accessibles aux candidats et admins
Route::middleware('auth:sanctum')->group(function () {
    Route::get('sessions', [ConcoursSessionController::class, 'index']);
    Route::get('sessions/{id}', [ConcoursSessionController::class, 'show']);

    Route::post('sessions', [ConcoursSessionController::class, 'store']);
    Route::put('sessions/{id}', [ConcoursSessionController::class, 'update']);
    Route::delete('sessions/{id}', [ConcoursSessionController::class, 'destroy']);
});

// Routes pour les candidats
Route::middleware(['auth:sanctum'])->group(function () {
    // Créer un enrôlement (avec paiement obligatoire)
    Route::post('enrollements', [EnrollementController::class, 'store']);

    // Voir son enrôlement
    Route::get('enrollements/{id}', [EnrollementController::class, 'show']);

    // Exporter son enrôlement en PDF
    Route::get('enrollements/{id}/export-pdf', [EnrollementController::class, 'exportPdf']);
});

// Routes réservées aux admins
Route::middleware(['auth:sanctum'])->group(function () {
    // Lister tous les enrôlements
    Route::get('enrollements', [EnrollementController::class, 'index']);

    // Valider / Refuser un enrôlement
    Route::put('enrollements/{id}', [EnrollementController::class, 'update']);

    // Supprimer un enrôlement
    Route::delete('enrollements/{id}', [EnrollementController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->group(function () {

    // 👨‍🎓 CANDIDAT
    Route::post('/paiements', [PaiementController::class, 'store']);
    Route::get('/mes-paiements', [PaiementController::class, 'mesPaiements']);

    // 👨‍💼 ADMIN
    /*Route::middleware('role:admin')->group(function () {
        Route::get('/paiements', [PaiementController::class, 'index']);
        Route::put('/paiements/{id}', [PaiementController::class, 'update']);
    });*/
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('centre-examen', [CentreExamenController::class, 'index']);
    Route::get('centre-examen/{id}', [CentreExamenController::class, 'show']);

    Route::post('centre-examen', [CentreExamenController::class, 'store']);
    Route::put('centre-examen/{id}', [CentreExamenController::class, 'update']);
    Route::delete('centre-examen/{id}', [CentreExamenController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('centre-examen-concours', [CentreExamenConcoursController::class, 'index']);
    Route::get('centre-examen-concours/{id}', [CentreExamenConcoursController::class, 'show']);

    Route::post('centre-examen-concours', [CentreExamenConcoursController::class, 'store']);
    Route::put('centre-examen-concours/{id}', [CentreExamenConcoursController::class, 'update']);
    Route::delete('centre-examen-concours/{id}', [CentreExamenConcoursController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('salles', [SalleController::class, 'index']);
    Route::get('salles/{id}', [SalleController::class, 'show']);

    Route::post('salles', [SalleController::class, 'store']);
    Route::put('salles/{id}', [SalleController::class, 'update']);
    Route::delete('salles/{id}', [SalleController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('centre-depot', [CentreDepotController::class, 'index']);
    Route::get('centre-depot/{id}', [CentreDepotController::class, 'show']);

    Route::post('centre-depot', [CentreDepotController::class, 'store']);
    Route::put('centre-depot/{id}', [CentreDepotController::class, 'update']);
    Route::delete('centre-depot/{id}', [CentreDepotController::class, 'destroy']);
});
