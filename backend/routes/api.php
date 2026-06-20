<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PowerBiExportController;
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

Route::get('/tickets/qrcode/{code}', [TicketController::class, 'qrcodeByCode']);
Route::get('/tickets/verify/{code}', [TicketController::class, 'verifyByCode']);

Route::get('/files', [FileController::class, 'index']);
Route::get('/files/{id}/content', [FileController::class, 'content'])->name('files.content');

Route::post('/contact', [ContactController::class, 'send']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('reservations', ReservationController::class)->except(['update']);

    Route::post('/files', [FileController::class, 'store']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);

    Route::get('/stats/overview', [StatController::class, 'overview']);
    Route::get('/stats/activity', [StatController::class, 'activity']);

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('events', EventController::class)->except(['index', 'show']);
        Route::apiResource('salles', SalleController::class)->except(['index', 'show']);

        Route::patch('/tickets/verify/{code}/use', [TicketController::class, 'useByCode']);

        Route::apiResource('tickets', TicketController::class)->except(['store']);

        Route::get('/powerbi/events.csv', [PowerBiExportController::class, 'events']);
        Route::get('/powerbi/reservations.csv', [PowerBiExportController::class, 'reservations']);
        Route::get('/powerbi/tickets.csv', [PowerBiExportController::class, 'tickets']);
        Route::get('/powerbi/salles.csv', [PowerBiExportController::class, 'salles']);
        Route::get('/powerbi/activity.csv', [PowerBiExportController::class, 'activity']);
        Route::get('/powerbi/stats.csv', [PowerBiExportController::class, 'stats']);
    });
});
