<?php

namespace App\Http\Controllers\Auth;

use Laravel\Fortify\Http\Controllers\RegisteredUserController as FortifyRegisteredUserController;
use Illuminate\Http\Request;

class RegisterController extends FortifyRegisteredUserController
{
    /**
     * Redirect path after registration.
     *
     * @var string
     */
    protected $redirectTo = '/chat';

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $response = parent::store($request);

        // Force redirect to /chat after successful registration
        if ($response->status() === 302 || $response->status() === 301) {
            return redirect('/chat');
        }

        return $response;
    }
}
