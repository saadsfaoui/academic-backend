<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckBlockedUser;
use App\Http\Controllers\ContactController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Routes protégées pour les utilisateurs et les groupes (admin uniquement)
Route::middleware(['auth:sanctum', CheckAdmin::class])->group(function () {
    // Gestion des utilisateurs
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::put('/users/{id}/block', [UserController::class, 'blockUser']);
    Route::put('/users/{id}/unblock', [UserController::class, 'unblockUser']);

    // Gestion des groupes
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::post('/groups/{id}/links', [GroupController::class, 'addLinks']);
    Route::delete('/groups/{id}/links', [GroupController::class, 'removeLink']);
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']);
    Route::put('/groups/{id}', [GroupController::class, 'update']);

    // Gestion des requêtes
    Route::get('/requests', [RequestController::class, 'index']);
    Route::put('/requests/{id}/approve', [RequestController::class, 'approve']);
    Route::put('/requests/{id}/reject', [RequestController::class, 'reject']);
    Route::delete('/requests/{id}', [RequestController::class, 'destroy']);

    // Routes d'administration
    Route::get('/admin/users/count', [AdminController::class, 'getUserCount']);
    Route::get('/admin/groups/count', [AdminController::class, 'getGroupCount']);
    Route::get('/admin/requests/pending/count', [AdminController::class, 'getPendingRequestCount']);
});

// L'utilisateur peut créer une requête sans être admin
Route::middleware(['auth:sanctum', CheckBlockedUser::class])->group(function () {
    Route::post('/requests', [RequestController::class, 'store']);
});

// Gestion des matières
// Gestion des matières
Route::middleware('auth:sanctum')->prefix('subjects')->group(function () {
    Route::get('/', [SubjectController::class, 'index']);
    Route::post('/', [SubjectController::class, 'store']);
    Route::put('/{id}', [SubjectController::class, 'update']);
    Route::delete('/{id}', [SubjectController::class, 'destroy']);
});

// Gestion des prédictions
Route::middleware('auth:sanctum')->prefix('predictions')->group(function () {
    Route::post('/generate', [PredictionController::class, 'generate']);
    Route::get('/strengths/{studentName}', [PredictionController::class, 'strengths']);
    Route::get('/user', [PredictionController::class, 'getUserPredictions']); // Récupérer les prédictions de l'utilisateur connecté
    Route::get('/overview', [PredictionController::class, 'getPredictionsOverview']); // Vue générale des prédictions

});

// Tableau de bord
Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'index']);
Route::middleware('auth:sanctum')->get('/dashboard/subjects-proportion', [DashboardController::class, 'getSubjectsProportion']);

// Gestion des groupes pour les utilisateurs connectés
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/groups/search', [GroupController::class, 'searchGroups']);
    Route::get('/groups/recommended', [GroupController::class, 'recommendedGroups']);
    Route::get('/groups/resources', [GroupController::class, 'sharedResources']);
    Route::post('/groups/join/{id}', [GroupController::class, 'join']);
});





Route::post('/contact', [ContactController::class, 'sendContactMessage']);
