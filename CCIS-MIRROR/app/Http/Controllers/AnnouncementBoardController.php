<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnnouncementBoardController extends Controller
{
    public function index()
    {
        // 1. Fetch & Group Announcements
        // We use the view to get everything in one query
        $announcements = DB::table('all_announcements_view')
            ->orderBy('announcement_date', 'desc')
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
                    'author_avatar' => $first->author_avatar ?? null,
                    'likes_count'   => (int) ($first->likes_count ?? 0),
                    'date'          => Carbon::parse($first->announcement_date)->format('M d, Y h:i A'),
                    // Filter out null attachments if an announcement has none
                    'attachments'   => $group->whereNotNull('attachment_id')->map(fn($item) => [
                        'id'   => $item->attachment_id,
                        'path' => $item->file_path,
                        'type' => $item->file_type,
                    ])->values(),
                ];
            })->values();

        // 2. Fetch Upcoming Events (Standardized Format)
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

        // 3. Topic Stats
        // Grouping by topic directly in the DB is faster
        $stats = DB::table('all_announcements_view')
            ->select('topic', DB::raw('count(DISTINCT announcement_id) as total'))
            ->whereNotNull('topic')
            ->groupBy('topic')
            ->pluck('total', 'topic');

        return response()->json([
            'announcements'   => $announcements,
            'upcoming_events' => $upcomingEvents,
            'stats' => [
                'cs'  => $stats->get('Computer Science', 0),
                'it'  => $stats->get('Information Technology', 0),
                'is'  => $stats->get('Information Systems', 0),
                'lsg' => $stats->get('CCIS LSG', 0),
                'all' => $stats->sum(),
            ]
        ]);
    }
}