<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PredictionController extends Controller
{
    /**
     * Générer les prédictions trimestrielles pour l'utilisateur connecté.
     */
    public function getUserPredictions(Request $request)
    {
        $user = $request->user();

        $quarters = [
            'Q1' => [9, 10, 11],  // Septembre, Octobre, Novembre
            'Q2' => [12, 1, 2],   // Décembre, Janvier, Février
            'Q3' => [3, 4, 5],    // Mars, Avril, Mai
            'Q4' => [6, 7, 8],    // Juin, Juillet, Août
        ];

        $currentYear = Carbon::now()->year;

        $quarterlyPredictions = [];

        foreach ($quarters as $quarter => $months) {
            $subjects = Subject::where('user_id', $user->id)
                ->whereIn(DB::raw('MONTH(date)'), $months)
                ->select('name', DB::raw('AVG(score) as average_score'))
                ->groupBy('name')
                ->get();

            $existingSubjects = $subjects->pluck('name')->toArray();

            // Ajouter les données réelles au quarter
            foreach ($subjects as $subject) {
                $quarterlyPredictions[] = [
                    'quarter' => $quarter,
                    'subject' => $subject->name,
                    'predicted_score' => round($subject->average_score, 2),
                ];
            }

            // Générer des prédictions pour les matières manquantes
            $allSubjects = Subject::where('user_id', $user->id)
                ->select('name')
                ->distinct()
                ->pluck('name')
                ->toArray();

            $missingSubjects = array_diff($allSubjects, $existingSubjects);

            foreach ($missingSubjects as $subjectName) {
                // Calculer la moyenne des scores existants pour la matière
                $historicalScores = Subject::where('user_id', $user->id)
                    ->where('name', $subjectName)
                    ->avg('score');

                $predictedScore = $historicalScores
                    ? round($historicalScores, 2) // Utiliser la moyenne des scores existants
                    : rand(60, 90); // Générer une valeur aléatoire si aucune donnée n'existe.

                $quarterlyPredictions[] = [
                    'quarter' => $quarter,
                    'subject' => $subjectName,
                    'predicted_score' => $predictedScore,
                ];
            }
        }

        return response()->json($quarterlyPredictions);
    }

    /**
     * Vue générale des prédictions mensuelles.
     */
    public function getPredictionsOverview(Request $request)
    {
        $user = $request->user();

        $overviewData = DB::table('subjects')
            ->where('user_id', $user->id)
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, AVG(score) as average_score')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return response()->json($overviewData);
    }
}
