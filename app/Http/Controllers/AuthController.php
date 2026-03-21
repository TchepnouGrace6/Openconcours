<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Inscription candidat au concours
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => ['required', Password::min(8)],
        ]);

        $user = Utilisateur::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'candidat',
        ]);

        Logs::create([
            'utilisateur_id' => $user->id,
            'action' => 'Inscription candidat',
            'details' => 'Email: '.$user->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Compte candidat créé avec succès',
            'utilisateur' => $user,
        ], 201);
    }

    // Création administrateur
    public function createAdmin(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => ['required', Password::min(8)],
        ]);

        if (! $request->user() || ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $admin = Utilisateur::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        Logs::create([
            'utilisateur_id' => $request->user()->id,
            'action' => 'Création administrateur',
            'details' => 'Email: '.$admin->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Administrateur créé avec succès',
            'utilisateur' => $admin,
        ], 201);
    }

    // Connexion
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = Utilisateur::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register'); // Crée la vue auth/register.blade.php
    }

    public function showLoginForm()
    {
        return view('auth.login'); // Crée la vue auth/login.blade.php
    }
}
