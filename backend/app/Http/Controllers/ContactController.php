<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{

    private const OWNER_EMAIL = 'eventify439@gmail.com';

    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            Mail::to(self::OWNER_EMAIL)->send(new ContactMessage(
                $data['name'],
                $data['email'],
                $data['message'],
            ));
        } catch (\Throwable $e) {
            Log::error("Échec de l'envoi du message de contact de {$data['email']} : {$e->getMessage()}");

            return response()->json([
                'message' => "Votre message n'a pas pu être envoyé pour le moment. Veuillez réessayer plus tard.",
            ], 502);
        }

        return response()->json([
            'message' => 'Votre message a bien été envoyé. Nous vous répondrons dès que possible.',
        ], 200);
    }
}
