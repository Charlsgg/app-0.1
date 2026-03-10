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
        // We validate 'position' because that is what Vue sends
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'position' => ['required', 'string'],
        ]);

        // 2. Map the data to your database column names
        // Your DB uses 'user_type', so we rename 'position' here
        $credentials = [
            'email'     => $request->email,
            'password'  => $request->password,
            'user_type' => $request->position, 
        ];

        $remember = $request->boolean('remember');

        // 3. Attempt to log the user in
        if (Auth::attempt($credentials, $remember)) {
            
            // Protect against session fixation
            $request->session()->regenerate();

            // Return success JSON
            return response()->json([
                'message' => 'Logged in successfully.',
                'redirect' => '/dashboard'
            ], 200);
        }

        // 4. Return 401 if login fails
        // Your Vue code will catch this in the 'else if (res.status === 401)' block
        return response()->json([
            'message' => 'Invalid email, password, or position.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}