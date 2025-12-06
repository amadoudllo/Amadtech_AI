<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminGuestOrAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Si l'utilisateur est un admin, le laisser accéder
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Si l'utilisateur est connecté mais n'est pas admin, le rediriger
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect('/chat');
        }

        // Si l'utilisateur n'est pas connecté, le laisser accéder à la page de login
        return $next($request);
    }
}
