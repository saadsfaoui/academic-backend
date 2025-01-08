<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\CheckBlockedUser;
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
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Routes protégées pour les utilisateurs et les groupes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Gestion des utilisateurs (seulement pour admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::put('/users/{id}/block', [UserController::class, 'blockUser']);
    Route::put('/users/{id}/unblock', [UserController::class, 'unblockUser']);

    // Gestion des groupes (seulement pour admin)
    Route::get('/groups', [GroupController::class, 'index']); // Lister les groupes
    Route::post('/groups', [GroupController::class, 'store']); // Créer un groupe
    Route::post('/groups/{id}/links', [GroupController::class, 'addLinks']); // Ajouter des liens
    Route::delete('/groups/{id}/links', [GroupController::class, 'removeLink']); // Supprimer un lien
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']); // Supprimer un groupe
    Route::put('/groups/{id}', [GroupController::class, 'update']);

    // Gestion des requêtes (seulement pour admin)
    Route::get('/requests', [RequestController::class, 'index']);
    Route::put('/requests/{id}/approve', [RequestController::class, 'approve']);
    Route::put('/requests/{id}/reject', [RequestController::class, 'reject']);
    Route::delete('/requests/{id}', [RequestController::class, 'destroy']);
});

// L'utilisateur peut créer une requête sans être admin


Route::middleware(['auth:sanctum', CheckBlockedUser::class])->group(function () {
    Route::post('/requests', [RequestController::class, 'store']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin/users/count', [AdminController::class, 'getUserCount']);
    Route::get('/admin/groups/count', [AdminController::class, 'getGroupCount']);
    Route::get('/admin/requests/pending/count', [AdminController::class, 'getPendingRequestCount']);
});
