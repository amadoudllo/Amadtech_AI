<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmailVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Si l'utilisateur est authentifié et son email n'est pas vérifié
        if (auth()->check() && !auth()->user()->email_verified_at) {
            auth()->logout();
            return redirect('/login')->with('error', 'Votre email doit être vérifié avant de continuer.');
        }

        return $next($request);
    }
}
