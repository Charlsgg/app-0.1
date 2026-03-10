<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     */
    public function index()
    {
        // Fetch all announcements with author details
        // We use 'author:user_id,name' to only grab necessary columns
        return Announcement::with('author:user_id,name')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string', // This will hold the HTML + Base64 images
            'board_id' => 'required|integer',
        ]);

        // 2. Security Check: Ensure user is logged in
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            // 3. Create the announcement
            // Note: announcement_id will be handled by DB auto-increment if set up in migration
            $announcement = Announcement::create([
                'title'      => $request->title,
                'content' => $request->input('content'),
                'board_id'   => $request->board_id,
                'author_id'  => Auth::id(), 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Refresh and Load Relationship
            // This ensures the returned JSON includes the author's name immediately
            return response()->json($announcement->load('author:user_id,name'), 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save announcement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}