<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Points forts de l'étudiant
        $strengths = $this->getStrengths($user->id);

        // Performances globales de l'étudiant
        $overallPerformance = $this->getOverallPerformance($user->id);

        // Données pour le Pie Chart : Moyenne des scores par matière
        $subjectsProportion = $this->getSubjectsProportion($user->id);

        return response()->json([
            'strengths' => $strengths,
            'overall_performance' => $overallPerformance,
            'subjects_proportion' => $subjectsProportion,
        ]);
    }

    private function getStrengths($userId)
    {
        return DB::table('subjects')
            ->where('user_id', $userId)
            ->select('name', DB::raw('MAX(score) as max_score'))
            ->groupBy('name')
            ->orderBy('max_score', 'desc')
            ->take(3)
            ->get();
    }

    private function getOverallPerformance($userId)
    {
        $averageScore = Subject::where('user_id', $userId)->avg('score');

        $performanceOverTime = Subject::where('user_id', $userId)
            ->orderBy('date')
            ->select('date', 'score')
            ->get();

        return [
            'average_score' => $averageScore,
            'performance_over_time' => $performanceOverTime,
        ];
    }

    private function getSubjectsProportion($userId)
    {
        return DB::table('subjects')
            ->where('user_id', $userId)
            ->select('name', DB::raw('AVG(score) as average_score')) // Moyenne des scores
            ->groupBy('name')
            ->orderBy('average_score', 'desc')
            ->get()
            ->map(function ($item) {
                $item->average_score = (float) $item->average_score; // Convertir en float
                return $item;
            });
    }
}
