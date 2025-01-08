<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // Lister tous les groupes
    public function index()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $groups = Group::all();
        return response()->json($groups);
    }

    // Créer un nouveau groupe
    public function store(Request $request)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'links' => 'nullable|array',
            'links.*' => 'url', // Chaque lien doit être une URL valide
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'links' => $validated['links'] ?? [],
            'created_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Group created successfully', 'group' => $group]);
    }

    // Ajouter des liens à un groupe
    public function addLinks(Request $request, $id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $validated = $request->validate([
            'links' => 'required|array',
            'links.*' => 'url',
        ]);

        $group = Group::findOrFail($id);

        $currentLinks = $group->links ?? [];
        $group->links = array_merge($currentLinks, $validated['links']);
        $group->save();

        return response()->json(['message' => 'Links added successfully', 'group' => $group]);
    }

    // Supprimer un lien d'un groupe
    public function removeLink(Request $request, $id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $validated = $request->validate([
            'link' => 'required|url',
        ]);

        $group = Group::findOrFail($id);

        $currentLinks = $group->links ?? [];
        $group->links = array_filter($currentLinks, fn($l) => $l !== $validated['link']);
        $group->save();

        return response()->json(['message' => 'Link removed successfully', 'group' => $group]);
    }

    // Supprimer un groupe
    public function destroy($id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json(['message' => 'Group deleted successfully']);
    }
    public function update(Request $request, $id)
    {
        // Vérifier si l'utilisateur est admin
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        // Valider les données reçues
        $validated = $request->validate([
            'name' => 'required|string|max:255', // Nom du groupe
            'description' => 'nullable|string', // Description du groupe
            'links' => 'nullable|array',        // Liens du groupe
            'links.*' => 'url',                 // Chaque lien doit être une URL valide
        ]);

        // Rechercher le groupe par ID
        $group = Group::findOrFail($id);

        // Mettre à jour les données du groupe
        $group->name = $validated['name'];
        $group->description = $validated['description'] ?? $group->description;
        $group->links = $validated['links'] ?? $group->links;

        // Enregistrer les modifications
        $group->save();

        return response()->json([
            'message' => 'Group updated successfully.',
            'group' => $group
        ], 200);
    }
}
