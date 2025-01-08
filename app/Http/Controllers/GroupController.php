<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Prediction;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Rechercher des groupes par mot-clé (aléatoire).
     */
    public function searchGroups(Request $request)
    {
        $query = $request->input('query');

        // Rechercher des groupes par mot-clé dans une liste aléatoire
        $groups = Group::inRandomOrder()
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%$query%");
            })
            ->get();

        return response()->json($groups);
    }

    /**
     * Lister les groupes recommandés en fonction des prédictions faibles.
     */
    public function recommendedGroups(Request $request)
    {
        $user = $request->user(); // Utilisateur connecté

        // Obtenir les prédictions faibles (score < 60)
        $weakSubjects = Prediction::where('student_name', $user->name)
            ->where('predicted_score', '<', 60)
            ->pluck('subject'); // Récupérer les matières concernées

        // Trouver des groupes liés aux matières faibles
        $recommendedGroups = Group::where(function ($query) use ($weakSubjects) {
            foreach ($weakSubjects as $subject) {
                $query->orWhere('name', 'like', "%$subject%");
            }
        })->get();

        return response()->json($recommendedGroups);
    }

    /**
     * Lister toutes les ressources partagées.
     */
    public function sharedResources()
    {
        // Simulation des ressources partagées (remplacez par des données réelles si nécessaire)
        $sharedResources = [
            ['title' => 'Math Resources', 'description' => 'Learn algebra and calculus basics.'],
            ['title' => 'Science Club Materials', 'description' => 'Discover fun experiments.'],
            ['title' => 'History Discussions', 'description' => 'Explore ancient civilizations.'],
        ];

        return response()->json($sharedResources);
    }

    /**
     * Lister tous les groupes.
     */
    public function listGroups()
    {
        // Récupérer tous les groupes
        $groups = Group::all();

        return response()->json($groups);
    }

    /**
     * Créer un groupe.
     */
    /*public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:groups|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $group = Group::create($validated);

        return response()->json($group, 201);
    }*/

    /**
     * Rejoindre un groupe.
     */
    public function join(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $user = $request->user(); // Utilisateur connecté

        // Vérifier si l'utilisateur est déjà membre du groupe
        if ($user->groups()->where('group_id', $id)->exists()) {
            return response()->json([
                'message' => 'You are already a member of this group.',
            ], 400);
        }

        // Ajouter l'utilisateur au groupe
        $user->groups()->attach($group->id);

        return response()->json([
            'message' => "You have successfully joined the group: $group->name",
        ]);
    }
}

