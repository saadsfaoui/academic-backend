<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PredictionService;

class PredictionController extends Controller
{
    protected $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    // Générer des prédictions pour un étudiant
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string',
            'subjects' => 'required|array',
            'subjects.*' => 'string',
        ]);

        $predictions = $this->predictionService->generatePredictions(
            $validated['student_name'],
            $validated['subjects']
        );

        return response()->json($predictions, 201);
    }

    // Obtenir les forces d'un étudiant
    public function strengths($studentName)
    {
        $strengths = $this->predictionService->getStrengths($studentName);

        return response()->json($strengths);
    }
}

