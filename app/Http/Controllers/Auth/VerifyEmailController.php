<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VerifyEmailController extends Controller
{
    /**
     * Vérifier l'email et créer l'utilisateur
     */
    public function verify(Request $request, $token = null)
    {
        // Accepter le token soit en paramètre URL, soit en query string
        if (!$token) {
            $token = $request->query('token');
        }

        if (!$token) {
            return redirect('/login')->with('error', 'Token de vérification manquant');
        }

        try {
            // Vérifier le token
            $verification = DB::table('email_verifications')
                ->where('token', hash('sha256', $token))
                ->where('expires_at', '>', now())
                ->first();

            if (!$verification) {
                return redirect('/login')->with('error', 'Token de vérification invalide ou expiré');
            }

            // Vérifier si l'utilisateur existe déjà
            $user = User::where('email', $verification->email)->first();

            if (!$user) {
                // Récupérer les données d'enregistrement temporaires depuis la session
                $sessionKey = 'pending_registration_' . hash('md5', $verification->email);
                $registrationData = session($sessionKey);

                if (!$registrationData) {
                    // Si pas de données en session, créer un utilisateur minimal
                    $user = User::create([
                        'name' => 'User',
                        'email' => $verification->email,
                        'email_verified_at' => now(),
                        'password' => bcrypt(str_random(16)),
                    ]);
                } else {
                    // Créer l'utilisateur avec les données d'enregistrement
                    $user = User::create([
                        'name' => $registrationData['name'],
                        'email' => $registrationData['email'],
                        'password' => $registrationData['password'],
                        'phone' => $registrationData['phone'],
                        'country_code' => $registrationData['country_code'],
                        'email_verified_at' => now(),
                    ]);
                    
                    // Supprimer les données de session
                    session()->forget($sessionKey);
                }
            } else {
                // Mettre à jour la date de vérification
                $user->update(['email_verified_at' => now()]);
            }

            // Supprimer la vérification
            DB::table('email_verifications')
                ->where('email', $verification->email)
                ->delete();

            // Connecter automatiquement l'utilisateur
            auth()->login($user);

            return redirect('/chat')->with('success', 'Email vérifié avec succès! Bienvenue.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors de la vérification: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Une erreur s\'est produite lors de la vérification');
        }
    }
}
