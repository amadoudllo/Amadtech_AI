<?php

namespace App\Http\Controllers\Auth;

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\LoginRequest;

class LoginController extends FortifyAuthenticatedSessionController
{
    /**
     * Redirect path after authentication.
     *
     * @var string
     */
    protected $redirectTo = '/chat';

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $response = parent::store($request);

        // Force redirect to /chat after successful authentication
        if ($response->status() === 302 || $response->status() === 301) {
            return redirect('/chat');
        }

        return $response;
    }
}

