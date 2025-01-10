<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function store(Request $request)
    {


        $user = Auth::user();

        /* // Vérifier si l'utilisateur est bloqué
        if ($user->is_blocked) {
            return response()->json([
                'message' => 'You cannot create a request because your account is blocked.'
            ], 403);
        }*/

        // Validation
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        // Créer une nouvelle requête
        $newRequest = \App\Models\Request::create([
            'user_id' => $user->id,
            'group_id' => $validated['group_id'],
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Request created successfully.', 'request' => $newRequest], 201);
    }



    public function index()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $requests = \App\Models\Request::with(['user', 'group'])->get();
        return response()->json($requests);
    }


    public function approve($id)
    {
        // Vérifier si l'utilisateur connecté est admin
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        // Récupérer la demande
        $request = \App\Models\Request::findOrFail($id);

        // Marquer la demande comme approuvée
        $request->status = 'approved';
        $request->save();

        // Récupérer l'utilisateur et le groupe associés à la demande
        $user = $request->user;
        $group = $request->group;

        // Ajouter l'utilisateur au groupe
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            $user->groups()->attach($group->id);
        }

        return response()->json([
            'message' => 'Request approved successfully and user joined the group.',
            'request' => $request
        ]);
    }


    public function reject($id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $request = \App\Models\Request::findOrFail($id);
        $request->status = 'rejected';
        $request->save();

        return response()->json(['message' => 'Request rejected successfully.', 'request' => $request]);
    }


    public function destroy($id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        // Trouver la requête par son ID
        $request = \App\Models\Request::find($id);

        // Vérifier si la requête existe
        if (!$request) {
            return response()->json(['message' => 'Request not found.'], 404);
        }

        // Supprimer la requête
        $request->delete();

        return response()->json(['message' => 'Request deleted successfully.'], 200);
    }
}
