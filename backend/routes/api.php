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

/*
|--------------------------------------------------------------------------
| Authentification (Laravel Sanctum - tokens Bearer pour l'app Angular)
|--------------------------------------------------------------------------
*/
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});

/*
|--------------------------------------------------------------------------
| Lecture publique : consultation des événements/salles/billets sans connexion
|--------------------------------------------------------------------------
*/
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/salles', [SalleController::class, 'index']);
Route::get('/salles/{salle}', [SalleController::class, 'show']);
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);

// Formulaire de contact (page d'accueil) : envoie un email au propriétaire du site
Route::post('/contact', [ContactController::class, 'send']);

/*
|--------------------------------------------------------------------------
| Routes protégées : nécessitent un token Sanctum valide
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Réservations : un utilisateur connecté peut réserver / consulter / annuler
    Route::apiResource('reservations', ReservationController::class)->except(['update']);

    // Fichiers : upload/listage/suppression de pièces jointes (images, justificatifs...)
    Route::get('/files', [FileController::class, 'index']);
    Route::post('/files', [FileController::class, 'store']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);

    // Statistiques & audit (consultables par tout utilisateur connecté ; affinage possible par rôle)
    Route::get('/stats/overview', [StatController::class, 'overview']);
    Route::get('/stats/activity', [StatController::class, 'activity']);

    // Gestion (CRUD complet) réservée aux administrateurs
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('events', EventController::class)->except(['index', 'show']);
        Route::apiResource('salles', SalleController::class)->except(['index', 'show']);
        Route::apiResource('tickets', TicketController::class)->except(['index', 'show']);
    });
});
