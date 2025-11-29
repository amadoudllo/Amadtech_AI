<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAuthenticatedToDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If the response is a redirect to /dashboard, redirect to /chat instead
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            $targetPath = $response->getTargetUrl();
            
            // Check if redirecting to dashboard
            if (str_contains($targetPath, '/dashboard')) {
                return redirect('/chat');
            }
        }

        return $response;
    }
}
