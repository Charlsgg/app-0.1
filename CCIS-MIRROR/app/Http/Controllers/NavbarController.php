<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Don't forget to import this!

class NavbarController extends Controller
{
    public function getUserData(Request $request)
    {
        $user = $request->user()->load('profile');

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Get the raw path from the database
        $rawPath = $user->profile?->profile_picture;
        $avatarUrl = null;

        // If a picture exists, generate the full public URL for it
        if ($rawPath) {
            // Option A: If your DB stores just the filename, and they are in the 'avatars' folder:
            // $avatarUrl = asset('storage/avatars/' . $rawPath);
            
            // Option B: If your DB stores the relative path (e.g., 'avatars/filename.jpg'):
            $avatarUrl = asset('storage/' . $rawPath);
        }

        return response()->json([
            'name' => $user->name,
            'profile_picture' => $avatarUrl, 
        ]);
    }
}