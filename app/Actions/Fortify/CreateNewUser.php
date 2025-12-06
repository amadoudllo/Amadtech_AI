<?php

namespace App\Actions\Fortify;

use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        // Créer l'utilisateur SANS enregistrer dans la base de données (utilisateur temporaire)
        $user = new User([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        // Créer un token de vérification d'email
        $token = hash('sha256', $input['email'] . time() . random_bytes(32));
        
        // Enregistrer le token dans email_verifications (l'utilisateur n'existe pas encore dans users)
        EmailVerification::create([
            'email' => $input['email'],
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        // Envoyer un email de confirmation
        // À implémenter selon votre système de mail

        return $user;
    }
}
