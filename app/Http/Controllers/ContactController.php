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
        $adminEmail = 'your_admin_email@example.com';

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
    }
}
