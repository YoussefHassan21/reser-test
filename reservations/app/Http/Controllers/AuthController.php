<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Recherche de l'utilisateur par e-mail
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->password === $credentials['password']) {
            // Connecte l'utilisateur manuellement sans hachage
            Auth::login($user);
            
            // Redirige vers la page des formulaires (forms)
            return redirect()->route('forms.index'); 
        }

        // Si la connexion échoue, rediriger vers la page de connexion avec un message d'erreur
        return redirect()->route('login')->withErrors('Email ou mot de passe incorrect.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
