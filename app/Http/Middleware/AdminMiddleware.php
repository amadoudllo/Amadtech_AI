<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->role !== 'admin') {
            abort(403, 'Accès refusé. Vous devez être administrateur.');
        }

        if ($user->is_blocked) {
            abort(403, 'Votre compte a été bloqué.');
        }

        return $next($request);
    }
}
