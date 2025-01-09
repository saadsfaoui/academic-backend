<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Récupérer tous les sujets de l'utilisateur connecté.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user(); // Récupérer l'utilisateur connecté
        $subjects = $user->subjects; // Récupérer les sujets associés à cet utilisateur

        return response()->json($subjects);
    }

    /**
     * Ajouter un nouveau sujet et enregistrer dans grades.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'score' => 'required|integer|min:0|max:100',
            'date' => 'required|date',
        ]);

        $user = $request->user(); // Récupérer l'utilisateur connecté

        // Créer le sujet
        $subject = $user->subjects()->create($validated);

        // Ajouter les données dans la table grades
        DB::table('grades')->insert([
            'student_name' => $user->name, // Nom de l'utilisateur connecté
            'subject' => $subject->name,
            'score' => $subject->score,
            'quarter' => 'N/A', // Vous pouvez adapter ce champ si nécessaire
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Subject created and added to grades'], 201);
    }

    /**
     * Mettre à jour un sujet existant et synchroniser avec grades.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'score' => 'sometimes|required|integer|min:0|max:100',
            'date' => 'sometimes|required|date',
        ]);

        $user = $request->user(); // Récupérer l'utilisateur connecté
        $subject = $user->subjects()->findOrFail($id);

        // Mettre à jour le sujet
        $subject->update($validated);

        // Mettre à jour également dans la table grades
        DB::table('grades')
            ->where('student_name', $user->name)
            ->where('subject', $subject->name)
            ->update([
                'score' => $subject->score,
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Subject updated and grades synchronized'], 200);
    }

    /**
     * Supprimer un sujet et l'enlever de grades.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user(); // Récupérer l'utilisateur connecté
        $subject = $user->subjects()->findOrFail($id);

        // Supprimer le sujet
        $subject->delete();

        // Supprimer également de la table grades
        DB::table('grades')
            ->where('student_name', $user->name)
            ->where('subject', $subject->name)
            ->delete();

        return response()->json(['message' => 'Subject and associated grade deleted'], 200);
    }
}
