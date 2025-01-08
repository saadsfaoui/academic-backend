<?php

/*namespace App\Services;

use App\Models\Prediction;
use Illuminate\Support\Facades\DB;

class PredictionService
{
    // Générer des prédictions trimestrielles pour un étudiant
    public function generatePredictions($studentName, $subjects)
    {
        $predictions = [];

        foreach ($subjects as $subject) {
            // Récupérer les scores historiques pour l'étudiant et la matière
            $historicalScores = DB::table('grades')
                ->where('student_name', $studentName)
                ->where('subject', $subject)
                ->pluck('score');

            // Calculer la moyenne des scores historiques
            $predictedScore = $historicalScores->average() ?? 0;

            // Générer les prédictions pour chaque trimestre
            foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                $predictions[] = Prediction::create([
                    'student_name' => $studentName,
                    'subject' => $subject,
                    'predicted_score' => $predictedScore,
                    'quarter' => $quarter,
                ]);
            }
        }

        return $predictions;
    }


    // Récupérer les forces d'un étudiant (matières avec les meilleurs scores)
    public function getStrengths($studentName)
    {
        return Prediction::where('student_name', $studentName)
            ->orderBy('predicted_score', 'desc')
            ->take(3)
            ->get();
    }
}*/


namespace App\Services;

use App\Models\Prediction;
use Illuminate\Support\Facades\DB;

class PredictionService
{
    /**
     * Générer des prédictions trimestrielles pour un étudiant.
     *
     * @param string $studentName
     * @param array $subjects
     * @return array
     */
    public function generatePredictions($studentName, $subjects)
    {
        $predictions = [];

        foreach ($subjects as $subject) {
            // Récupérer les scores historiques pour l'étudiant et la matière
            $historicalScores = DB::table('grades')
                ->where('student_name', $studentName)
                ->where('subject', $subject)
                ->pluck('score');

            // Calculer la moyenne des scores historiques
            $predictedScore = $historicalScores->average() ?? 0;

            // Générer les prédictions pour chaque trimestre
            foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                $predictions[] = Prediction::create([
                    'student_name' => $studentName,
                    'subject' => $subject,
                    'predicted_score' => $predictedScore,
                    'quarter' => $quarter,
                ]);
            }
        }

        return $predictions;
    }

    /**
     * Récupérer les forces d'un étudiant (au maximum une par matière).
     *
     * @param string $studentName
     * @return array
     */
    public function getStrengths($studentName)
    {
        // Récupérer les prédictions groupées par matière avec le meilleur score pour chaque matière
        $strengths = Prediction::where('student_name', $studentName)
            ->select('subject', DB::raw('MAX(predicted_score) as max_score'))
            ->groupBy('subject')
            ->orderBy('max_score', 'desc')
            ->get();

        // Convertir en un format lisible
        return $strengths->map(function ($strength) use ($studentName) {
            return [
                'student_name' => $studentName,
                'subject' => $strength->subject,
                'strength' => "Excellent in {$strength->subject} with a score of {$strength->max_score}",
            ];
        })->toArray();
    }
}


