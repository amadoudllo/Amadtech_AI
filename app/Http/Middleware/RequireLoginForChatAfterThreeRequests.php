<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireLoginForChatAfterThreeRequests
{
    /**
     * Handle an incoming request.
     * For guest users, count chat requests in session and require login after 3 requests.
     */
    public function handle(Request $request, Closure $next)
    {
        // If user is authenticated, allow
        if ($request->user()) {
            return $next($request);
        }

        // Count requests for guest users in session (only count POST requests = message sends)
        $count = (int) $request->session()->get('chat_guest_count', 0);

        // Only increment on POST (message send) — do NOT count GET (page view)
        if ($request->isMethod('post')) {
            $count++;
            $request->session()->put('chat_guest_count', $count);
        }

        if ($count > 3) {
            // For POST requests, always return JSON (even if not explicitly requested)
            if ($request->isMethod('post')) {
                return response()->json([
                    'error' => 'auth_required',
                    'message' => 'Veuillez vous connecter ou vous inscrire pour continuer.',
                ], 401);
            }

            // Otherwise (for GET), redirect to login
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
