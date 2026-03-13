<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validate the incoming JSON
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'position' => ['required', 'string'],
        ]);

        // 2. Map the data to your database column names
        $credentials = [
            'email'     => $request->email,
            'password'  => $request->password,
            'user_type' => $request->position, 
        ];

        $remember = $request->boolean('remember');

        // 3. Attempt to log the user in
        if (Auth::attempt($credentials, $remember)) {
            
            $request->session()->regenerate();
            $redirectUrl = match (Auth::user()->user_type) {
                'it_instructor' => '/it/home-page',
                'cs_instructor' => '/cs/home-page',
                'is_instructor' => '/is/home-page',
                'lsg_officer'   => '/lsg/home-page',
            };

            return response()->json([
                'message'  => 'Logged in successfully.',
                'redirect' => $redirectUrl
            ], 200);
        }
    
        return response()->json([
            'message' => 'Invalid email, password, or position.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out successfully.'], 200);
        }

        return redirect('/login');
    }
}