<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// use Inertia\Inertia; // Uncomment if using Inertia.js

class UserAnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Fetch raw data from our SQL view
        $rawData = DB::table('user_announcements_attachments_view')
            ->where('author_id', $user->user_id) // Match your user PK
            ->orderBy('announcement_date', 'desc')
            ->get();

        // 2. Group the flat rows by announcement_id
        $groupedAnnouncements = $rawData->groupBy('announcement_id');

        // 3. Map the data to match your Vue component's interface
        $formattedAnnouncements = $groupedAnnouncements->map(function ($group) use ($user) {
            $main = $group->first();

            // Extract valid attachments into an array
            // Extract valid attachments into an array
            $attachments = $group->filter(fn($item) => !is_null($item->attachment_id))
                ->map(fn($item) => [
                    'attachment_id' => $item->attachment_id,
                    'file_type' => $item->file_type,
                    'file_path' => $item->file_path,
                    // REMOVED 'file_name' since it is not in your database
                ])->values()->toArray();
            return [
                'id' => $main->announcement_id,
                'title' => $main->title,
                'content' => $main->content,
                'topic' => $main->topic,
                // Format the date however you prefer (e.g., "2 days ago" or "Oct 24, 2023")
                'date' => Carbon::parse($main->announcement_date)->diffForHumans(), 
                'likes_count' => 0, // Placeholder: Add likes table logic if needed later
                'author_name' => $user->name, // Since they only view their own, it's always this user
                'author_avatar' => null, // Update this if you have the profile_picture path
                'attachments' => $attachments,
            ];
        })->values()->toArray();

        // 4. Return to Vue
        
        // IF USING INERTIA.JS:
        // return Inertia::render('Announcements/Index', [
        //     'announcements' => $formattedAnnouncements
        // ]);

        // IF USING A STANDARD API/AXIOS:
        return response()->json([
            'announcements' => $formattedAnnouncements
        ]);
    }
}