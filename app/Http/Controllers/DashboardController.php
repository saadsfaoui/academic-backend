<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord de l'étudiant.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Récupérer les points forts de l'étudiant
        $strengths = $this->getStrengths($user->id);

        // Récupérer les performances globales
        $overallPerformance = $this->getOverallPerformance($user->id);

        return response()->json([
            'strengths' => $strengths,
            'overall_performance' => $overallPerformance,
        ]);
    }

    /**
     * Obtenir les points forts de l'étudiant.
     *
     * @param int $userId
     * @return array
     */
    private function getStrengths($userId)
    {
    // Récupérer les matières avec le meilleur score
     $strengths = DB::table('subjects')
        ->where('user_id', $userId)
        ->select('name', DB::raw('MAX(score) as max_score'))
        ->groupBy('name')
        ->orderBy('max_score', 'desc') // Tri par le score maximum
        ->take(3)
        ->get();

     return $strengths;
    }

    /**
     * Obtenir les performances globales de l'étudiant.
     *
     * @param int $userId
     * @return array
     */
    private function getOverallPerformance($userId)
    {
        // Calculer la moyenne générale
        $averageScore = Subject::where('user_id', $userId)
            ->avg('score');

        // Récupérer l'évolution des notes par date
        $performanceOverTime = Subject::where('user_id', $userId)
            ->orderBy('date')
            ->select('date', 'score')
            ->get();

        return [
            'average_score' => $averageScore,
            'performance_over_time' => $performanceOverTime,
        ];
    }
}
