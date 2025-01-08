<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Lister tous les utilisateurs
    public function index()
    {

        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }


        $users = User::all();
        return response()->json($users);
    }

    // Bloquer un utilisateur
    public function blockUser($id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $user = User::findOrFail($id);
        $user->is_blocked = true;
        $user->save();

        return response()->json(['message' => 'User blocked successfully']);
    }

    // Débloquer un utilisateur
    public function unblockUser($id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $user = User::findOrFail($id);
        $user->is_blocked = false;
        $user->save();

        return response()->json(['message' => 'User unblocked successfully']);
    }


    public function show($id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        // Rechercher l'utilisateur par son ID
        $user = User::find($id);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Retourner l'utilisateur trouvé
        return response()->json($user, 200);
    }

    // Méthode pour mettre à jour un utilisateur par ID
    public function update(Request $request, $id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $user = User::findOrFail($id);

        $user->update($request->only(['name', 'role'])); // Mettez à jour uniquement les champs nécessaires

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200); // Retournez les données mises à jour
    }

    public function destroy($id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
