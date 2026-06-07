<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    /**
     * Inscription d'un nouvel utilisateur (rôle "user" par défaut).
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $this->activityLogger->log($user, 'register', "Inscription de {$user->email}", $request);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Connexion : retourne un token Sanctum + l'utilisateur authentifié.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            $this->activityLogger->log(null, 'login_failed', "Tentative échouée pour {$credentials['email']}", $request);

            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $this->activityLogger->log($user, 'login', "Connexion de {$user->email}", $request);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Déconnexion : révoque le token courant.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();

        $this->activityLogger->log($user, 'logout', "Déconnexion de {$user->email}", $request);

        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    /**
     * Retourne l'utilisateur authentifié courant.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
