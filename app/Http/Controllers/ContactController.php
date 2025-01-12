<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendContactMessage(Request $request)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Adresse email de l'administrateur
        $adminEmail = 'example@gmail.com'; // Remplacez par votre email administrateur réel

        try {
            // Envoyer l'email
            Mail::send('emails.contact', [
                'name' => $request->name,
                'email' => $request->email,
                'messageContent' => $request->message,
            ], function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('Nouveau message de contact');
            });

            return response()->json(['message' => 'Votre message a été envoyé avec succès.'], 200);
        } catch (\Exception $e) {
            // Retourner une erreur si l'envoi échoue
            return response()->json([
                'error' => 'Une erreur est survenue lors de l\'envoi du message.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
