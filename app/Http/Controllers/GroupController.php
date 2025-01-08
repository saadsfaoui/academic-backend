<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Prediction;
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
            'links.*' => 'url',
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

    // Mettre à jour un groupe
    public function update(Request $request, $id)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'links' => 'nullable|array',
            'links.*' => 'url',
        ]);

        $group = Group::findOrFail($id);
        $group->name = $validated['name'];
        $group->description = $validated['description'] ?? $group->description;
        $group->links = $validated['links'] ?? $group->links;
        $group->save();

        return response()->json([
            'message' => 'Group updated successfully.',
            'group' => $group,
        ], 200);
    }

    // Rechercher des groupes par mot-clé
    public function searchGroups(Request $request)
    {
        $query = $request->input('query');
        $groups = Group::inRandomOrder()
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%$query%");
            })
            ->get();

        return response()->json($groups);
    }

    // Lister les groupes recommandés en fonction des prédictions faibles
    /* public function recommendedGroups(Request $request)
    {
        $user = $request->user();
        $weakSubjects = Prediction::where('student_name', $user->name)
            ->where('predicted_score', '<', 60)
            ->pluck('subject');

        $recommendedGroups = Group::where(function ($query) use ($weakSubjects) {
            foreach ($weakSubjects as $subject) {
                $query->orWhere('name', 'like', "%$subject%");
            }
        })->get();

        return response()->json($recommendedGroups);
    }*/


    public function recommendedGroups(Request $request)
    {
        $user = $request->user(); // Récupérer l'utilisateur connecté

        // Récupérer les matières avec des prédictions faibles
        $weakSubjects = Prediction::where('student_name', $user->name)
            ->where('predicted_score', '<', 60)
            ->pluck('subject');

        // Récupérer les groupes recommandés basés sur les matières faibles
        $recommendedGroups = Group::whereIn('name', $weakSubjects)->get();

        return response()->json($recommendedGroups);
    }

    // Lister toutes les ressources partagées
    public function sharedResources()
    {
        $sharedResources = [
            ['title' => 'Math Resources', 'description' => 'Learn algebra and calculus basics.'],
            ['title' => 'Science Club Materials', 'description' => 'Discover fun experiments.'],
            ['title' => 'History Discussions', 'description' => 'Explore ancient civilizations.'],
        ];

        return response()->json($sharedResources);
    }

    // Rejoindre un groupe
    public function join(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $user = $request->user();

        if ($user->groups()->where('group_id', $id)->exists()) {
            return response()->json(['message' => 'You are already a member of this group.'], 400);
        }

        $user->groups()->attach($group->id);

        return response()->json(['message' => "You have successfully joined the group: $group->name"]);
    }
}
