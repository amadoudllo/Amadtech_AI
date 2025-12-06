<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerifyEmailMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::min(8)],
                'phone' => ['required', 'regex:/^[0-9]{9,15}$/', 'max:20'],
                'country_code' => ['required', 'string', 'max:5'],
                'terms' => ['required', 'accepted'],
            ], [
                'name.required' => 'Le nom est obligatoire',
                'name.min' => 'Le nom doit avoir au moins 3 caractères',
                'email.required' => 'L\'email est obligatoire',
                'email.email' => 'L\'email doit être valide',
                'email.unique' => 'Cet email est déjà utilisé',
                'password.required' => 'Le mot de passe est obligatoire',
                'password.confirmed' => 'Les mots de passe ne correspondent pas',
                'password.min' => 'Le mot de passe doit avoir au moins 8 caractères',
                'phone.required' => 'Le numéro de téléphone est obligatoire',
                'phone.regex' => 'Le numéro de téléphone doit contenir 9 à 15 chiffres',
                'country_code.required' => 'Le code pays est obligatoire',
                'terms.required' => 'Vous devez accepter les conditions',
            ]);

            // Vérifier que l'email n'est pas déjà en attente de vérification
            $pendingVerification = DB::table('email_verifications')
                ->where('email', $validated['email'])
                ->where('expires_at', '>', now())
                ->first();

            if ($pendingVerification) {
                return response()->json([
                    'message' => 'Un email de vérification est déjà en attente pour cet email. Veuillez vérifier votre boîte de réception.',
                ], 422);
            }

            // NE PAS créer l'utilisateur tout de suite - le créer seulement après vérification de l'email
            // À la place, créer une entrée dans email_verifications
            
            // Supprimer les anciennes vérifications pour ce même email (s'il y en a)
            DB::table('email_verifications')
                ->where('email', $validated['email'])
                ->delete();

            // Générer un token de vérification
            $token = Str::random(64);
            $expiresAt = now()->addHours(24);

            // Stocker les données d'enregistrement temporairement avec le token
            DB::table('email_verifications')->insert([
                'email' => $validated['email'],
                'token' => hash('sha256', $token),
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Envoyer l'email de vérification
            try {
                Log::info('Envoi du lien de vérification à: ' . $validated['email']);
                
                // Créer temporairement l'utilisateur pour l'email
                $tempUser = new User([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);
                
                // Stocker les données pour la création après vérification
                session(['pending_registration_' . hash('md5', $validated['email']) => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'phone' => $validated['phone'],
                    'country_code' => $validated['country_code'],
                ]]);

                Mail::to($validated['email'])->send(new VerifyEmailMail($tempUser, $token));
                Log::info('Email de vérification envoyé avec succès à: ' . $validated['email']);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
                DB::table('email_verifications')
                    ->where('email', $validated['email'])
                    ->delete();
                return response()->json([
                    'message' => 'Erreur lors de l\'envoi de l\'email de vérification.',
                ], 500);
            }

            // Retourner une réponse JSON
            return response()->json([
                'message' => 'Un email de vérification a été envoyé à ' . $validated['email'] . '. Veuillez cliquer sur le lien pour confirmer votre inscriptions.',
                'email' => $validated['email'],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur s\'est produite lors de l\'inscription: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show email verification page
     */
    public function showVerifyEmail(Request $request)
    {
        $email = $request->query('email');
        return view('auth.verify-email', ['email' => $email]);
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
                'token' => ['required', 'string'],
            ]);

            $verification = DB::table('email_verifications')
                ->where('email', $validated['email'])
                ->where('token', hash('sha256', $validated['token']))
                ->first();

            // Token not found or expired
            if (!$verification) {
                return response()->json([
                    'message' => 'Le lien de vérification est invalide.',
                ], 422);
            }

            if ($verification->expires_at < now()) {
                DB::table('email_verifications')->where('id', $verification->id)->delete();
                return response()->json([
                    'message' => 'Le lien de vérification a expiré. Veuillez demander un nouveau lien.',
                ], 422);
            }

            // Mark email as verified
            User::find($verification->user_id)->update([
                'email_verified_at' => now(),
            ]);

            // Delete verification record
            DB::table('email_verifications')->where('id', $verification->id)->delete();

            return response()->json([
                'message' => 'Votre email a été vérifié avec succès! Vous pouvez maintenant vous connecter.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur s\'est produite: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email', 'exists:users'],
            ]);

            $user = User::where('email', $validated['email'])->first();

            // Check if already verified
            if ($user->email_verified_at) {
                return response()->json([
                    'message' => 'Votre email est déjà vérifié.',
                ], 200);
            }

            // Delete old verification token
            DB::table('email_verifications')->where('user_id', $user->id)->delete();

            // Generate new token
            $token = Str::random(64);
            $expiresAt = now()->addHours(24);

            DB::table('email_verifications')->insert([
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => hash('sha256', $token),
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send email
            try {
                Log::info('Resending verification email to: ' . $user->email);
                Mail::to($user->email)->send(new VerifyEmailMail($user, $token));
                Log::info('Verification email resent successfully to: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Error resending email: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Un nouveau lien de vérification a été envoyé à votre email.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur s\'est produite: ' . $e->getMessage(),
            ], 500);
        }
    }
}
