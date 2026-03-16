<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnnouncementBoardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get filter from request (matches the 'role' sent from Vue)
        $filter = $request->query('topic'); // cs_instructor, it_instructor, etc.

        // 2. Fetch Announcements with optional filtering
        $query = DB::table('all_announcements_view');

        if ($filter) {
            $query->where('author_type', $filter);
        }

        $announcements = $query->orderBy('announcement_date', 'desc')
            ->get()
            ->groupBy('announcement_id')
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'id'            => $first->announcement_id,
                    'title'         => $first->title,
                    'content'       => $first->content,
                    'topic'         => $first->topic ?? 'General',
                    'author_name'   => $first->author_name,
                    'author_type'   => $first->author_type,
                    'author_avatar' => $first->author_avatar ?? null,
                    'likes_count'   => (int) ($first->likes_count ?? 0),
                    'date'          => Carbon::parse($first->announcement_date)->format('M d, Y h:i A'),
                    'attachments'   => $group->whereNotNull('attachment_id')->map(fn($item) => [
                        'id'   => $item->attachment_id,
                        'path' => $item->file_path,
                        'type' => $item->file_type,
                    ])->values(),
                ];
            })->values();

        // 3. Fetch Upcoming Events (Standard logic)
        $upcomingEvents = DB::table('table_events')
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(3)
            ->get()
            ->map(function ($event) {
                $dt = Carbon::parse($event->start_time);
                return [
                    'id'    => $event->event_id,
                    'title' => $event->title,
                    'month' => $dt->format('M'),
                    'day'   => $dt->format('d'),
                    'time'  => $dt->format('g:i A'),
                ];
            });

        // 4. Calculate Stats (We run a separate query so stats don't disappear when filtering)
        $statsRaw = DB::table('all_announcements_view')
            ->select('author_type', DB::raw('count(DISTINCT announcement_id) as total'))
            ->groupBy('author_type')
            ->pluck('total', 'author_type');

        return response()->json([
            'announcements'   => $announcements,
            'upcoming_events' => $upcomingEvents,
            'active_filter'   => $filter, // Send back what is currently filtered
            'stats' => [
                'cs'  => (int) $statsRaw->get('cs_instructor', 0),
                'it'  => (int) $statsRaw->get('it_instructor', 0),
                'is'  => (int) $statsRaw->get('is_instructor', 0),
                'lsg' => (int) $statsRaw->get('lsg_officer', 0),
                'all' => (int) $statsRaw->sum(),
            ]
        ]);
    }
}