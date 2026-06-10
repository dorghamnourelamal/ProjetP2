<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\TicketController;

Route::get('/test', function () {
    return response()->json(['message' => 'API Laravel OK']);
});


Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});


Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/salles', [SalleController::class, 'index']);
Route::get('/salles/{salle}', [SalleController::class, 'show']);
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);

// IMPORTANT : route publique pour afficher les images dans Angular
Route::get('/files', [FileController::class, 'index']);

// Formulaire de contact (page d'accueil) : envoie un email au propriétaire du site
Route::post('/contact', [ContactController::class, 'send']);


Route::middleware('auth:sanctum')->group(function () {

    // Réservations : un utilisateur connecté peut réserver / consulter / annuler
    Route::apiResource('reservations', ReservationController::class)->except(['update']);

    // Fichiers : upload/suppression protégés
    Route::post('/files', [FileController::class, 'store']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);

    // Statistiques & audit
    Route::get('/stats/overview', [StatController::class, 'overview']);
    Route::get('/stats/activity', [StatController::class, 'activity']);

    // Gestion réservée aux administrateurs
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('events', EventController::class)->except(['index', 'show']);
        Route::apiResource('salles', SalleController::class)->except(['index', 'show']);
        Route::apiResource('tickets', TicketController::class)->except(['index', 'show']);
    });
});
