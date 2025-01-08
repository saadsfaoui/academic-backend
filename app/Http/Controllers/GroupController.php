<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class GroupController extends Controller
{
    /**
     * Rechercher des groupes et afficher les groupes recommandés.
     */
    public function index(Request $request)
    {
        $user = $request->user(); // Utilisateur connecté
        $query = $request->input('query');

        // Rechercher des groupes par mot-clé (sur une liste aléatoire)
        $groups = Group::inRandomOrder()
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'like', "%$query%");
            })
            ->get();

        // Obtenir les groupes recommandés en fonction des prédictions faibles
        $recommendedGroups = $this->getRecommendedGroups($user->id);

        // Lister les ressources partagées
        $sharedResources = $this->getSharedResources();

        return response()->json([
            'search_results' => $groups,
            'recommended_groups' => $recommendedGroups,
            'shared_resources' => $sharedResources,
        ]);
    }

    /**
     * Créer un groupe.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:groups|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $group = Group::create($validated);

        return response()->json($group, 201);
    }

    /**
     * Rejoindre un groupe.
     */
    public function join(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $user = $request->user(); // Obtenir l'utilisateur connecté

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

    /**
     * Obtenir les groupes recommandés en fonction des prédictions faibles.
     */
    private function getRecommendedGroups($userId)
    {
        $user = Auth::user(); // Remplacement de auth()->user() par Auth::user()
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        // Obtenir les prédictions faibles (exemple : score < 60)
        $weakSubjects = Prediction::where('student_name', $user->name)
            ->where('predicted_score', '<', 60)
            ->pluck('subject'); // Récupérer les matières concernées

        // Trouver des groupes liés aux matières faibles
        $recommendedGroups = Group::where(function ($query) use ($weakSubjects) {
            foreach ($weakSubjects as $subject) {
                $query->orWhere('name', 'like', "%$subject%");
            }
        })
        ->get();

        return $recommendedGroups;
    }

    /**
     * Obtenir la liste des ressources partagées.
     */
    private function getSharedResources()
    {
        // Simulation des ressources partagées (vous pouvez remplacer cela par des données réelles)
        $sharedResources = [
            ['title' => 'Math Resources', 'description' => 'Learn algebra and calculus basics.'],
            ['title' => 'Science Club Materials', 'description' => 'Discover fun experiments.'],
            ['title' => 'History Discussions', 'description' => 'Explore ancient civilizations.'],
        ];

        return $sharedResources;
    }
}
