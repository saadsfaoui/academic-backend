<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\GroupController;

Route::prefix('subjects')->group(function () {
    Route::get('/', [SubjectController::class, 'index']);       // Récupérer toutes les matières
    Route::post('/', [SubjectController::class, 'store']);      // Ajouter une matière
    Route::put('/{id}', [SubjectController::class, 'update']); // Mettre à jour une matière
    Route::delete('/{id}', [SubjectController::class, 'destroy']); // Supprimer une matière
});
Route::prefix('groups')->group(function () {
    Route::get('/', [GroupController::class, 'index']);          // Rechercher ou lister tous les groupes
    Route::post('/', [GroupController::class, 'store']);         // Ajouter un nouveau groupe
    Route::post('/join/{id}', [GroupController::class, 'join']); // Rejoindre un groupe
    
});
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']); // Inscription
    Route::post('login', [AuthController::class, 'login']);       // Connexion
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum'); // Déconnexion
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups/join/{id}', [GroupController::class, 'join']);
    //Route::post('/subjects', [SubjectController::class, 'store']);
});

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        /** @var \App\Models\MyUserModel $user **/
        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;
        
        return response()->json(['token' => $token], 200);
    }
    
    return response()->json(['message' => 'Invalid credentials'], 401);
}); 

use App\Http\Controllers\PredictionController;

Route::prefix('predictions')->group(function () {
    Route::post('/generate', [PredictionController::class, 'generate']); // Générer des prédictions
    Route::get('/strengths/{studentName}', [PredictionController::class, 'strengths']); // Obtenir les forces d'un étudiant
});


Route::middleware('auth:sanctum')->prefix('subjects')->group(function () {
    Route::get('/', [SubjectController::class, 'index']);
    Route::post('/', [SubjectController::class, 'store']);
    Route::put('/{id}', [SubjectController::class, 'update']);
    Route::delete('/{id}', [SubjectController::class, 'destroy']);
});

use App\Http\Controllers\DashboardController;

Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'index']);
