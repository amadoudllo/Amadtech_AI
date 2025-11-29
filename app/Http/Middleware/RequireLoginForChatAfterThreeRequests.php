<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireLoginForChatAfterThreeRequests
{
    /**
     * Handle an incoming request.
     * For guest users, count ONLY message sends (POST /chat/send) and require login after 3 sends.
     * GET requests for history/conversations are NOT counted (unlimited for guests).
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // If user is authenticated, allow everything
            if ($request->user()) {
                return $next($request);
            }
        } catch (\Exception $e) {
            // If auth check fails (DB connection issue), treat as guest
            // Log the error but don't fail the request
            \Log::warning('Auth check failed in middleware: ' . $e->getMessage());
        }

        // Only count POST requests to /chat/send (actual message sends)
        // GET requests to /api/chat/conversations are allowed unlimited for guests
        $isMessageSend = $request->isMethod('post') && $request->is('chat/send');

        if ($isMessageSend) {
            // Increment counter only for message sends
            $count = (int) $request->session()->get('chat_guest_count', 0);
            $count++;
            $request->session()->put('chat_guest_count', $count);

            if ($count > 3) {
                return response()->json([
                    'error' => 'auth_required',
                    'message' => 'Veuillez vous connecter ou vous inscrire pour continuer.',
                ], 401);
            }
        }

        return $next($request);
    }
}
