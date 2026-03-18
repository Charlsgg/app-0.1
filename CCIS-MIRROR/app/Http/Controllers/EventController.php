<?php

namespace App\Http\Controllers;

use App\Models\EventFilterView;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = EventFilterView::query();
        
        if ($request->filled('month')) {
            $query->where('event_month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('event_year', $request->year);
        }
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        $events = $query->get();
        return response()->json([
            'status' => 'success',
            'events' => $events
        ]);
    }

    public function upcoming(Request $request)
    {
        // Include events that haven't started yet OR haven't ended yet
        $events = EventFilterView::where(function ($query) {
                $query->where('start_time', '>=', now())
                      ->orWhere('end_time', '>=', now());
            })
            ->orderBy('start_time', 'asc') 
            ->take(6) 
            ->get();

        return response()->json([
            'status' => 'success',
            'events' => $events
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validate the incoming data
        $request->validate([
            'title' => 'required|string|max:255',
            'event_month' => 'required|integer|min:1|max:12',
            'event_year' => 'required|integer',
            'Venue' => 'required|string|max:255',
            'content' => 'required|string',
            'time' => 'required|string',
            'end_time' => 'nullable|string', // ADDED: Validate optional end time
            'day_range' => 'required|string', 
            'start_day' => 'required|integer|min:1|max:31', 
            'end_day' => 'nullable|integer|min:1|max:31', 
        ]);

        // Parse the user's selected start time (e.g., "14:30")
        $timeParts = explode(':', $request->time);
        $hours = (int) $timeParts[0];
        $minutes = (int) $timeParts[1];

        // Create the start time based on the specific start_day and time
        $startTime = Carbon::create($request->event_year, $request->event_month, $request->start_day, $hours, $minutes, 0);
        
        // Nullable & Specific End Time Logic
        $endTime = null;
        
        // Determine the actual end day (If no end_day is provided but an end_time IS, it's a same-day event)
        $actualEndDay = $request->end_day ?: ($request->filled('end_time') ? $request->start_day : null);

        if (!is_null($actualEndDay)) {
            $endHours = 23;
            $endMinutes = 59;
            $endSeconds = 59;

            // If the user provided a specific end time, parse it
            if ($request->filled('end_time')) {
                $endTimeParts = explode(':', $request->end_time);
                $endHours = (int) $endTimeParts[0];
                $endMinutes = (int) $endTimeParts[1];
                $endSeconds = 0; // Reset seconds since an exact time was given
            }

            $endTime = Carbon::create($request->event_year, $request->event_month, $actualEndDay, $endHours, $endMinutes, $endSeconds);
        }

        // 2. Save securely!
        $event = Event::create([
            'title' => $request->title,
            'content' => $request->input('content'),
            'venue' => $request->Venue, 
            'day_range' => $request->day_range, 
            'start_time' => $startTime,
            'end_time' => $endTime, // Will save as NULL if no end_day and no end_time were provided
            'user_id' => Auth::id(),
            'board_id' => 1, 
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // 1. Find the exact event
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }

        // 2. Validate the incoming data
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'event_month' => 'sometimes|required|integer|min:1|max:12',
            'event_year' => 'sometimes|required|integer',
            'Venue' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'time' => 'sometimes|required|string',
            'end_time' => 'nullable|string', // ADDED: Validate optional end time
            'day_range' => 'sometimes|required|string', 
            'start_day' => 'sometimes|required|integer|min:1|max:31',
            'end_day' => 'nullable|integer|min:1|max:31',
        ]);

        if ($request->has('title')) $event->title = $request->title;
        if ($request->has('content')) {
            $event->content = $request->input('content');
        }
        if ($request->has('Venue')) $event->venue = $request->Venue;
        if ($request->has('day_range')) $event->day_range = $request->day_range; 
        
        // Update dates and times if the necessary fields are present
        if ($request->has('start_day') && $request->has('event_month') && $request->has('event_year') && $request->has('time')) {
            
            $timeParts = explode(':', $request->time);
            $hours = (int) $timeParts[0];
            $minutes = (int) $timeParts[1];

            $startTime = Carbon::create($request->event_year, $request->event_month, $request->start_day, $hours, $minutes, 0);
            
            $endTime = null;
            $actualEndDay = $request->end_day ?: ($request->filled('end_time') ? $request->start_day : null);

            if (!is_null($actualEndDay)) {
                $endHours = 23;
                $endMinutes = 59;
                $endSeconds = 59;

                // If the user provided a specific end time, parse it
                if ($request->filled('end_time')) {
                    $endTimeParts = explode(':', $request->end_time);
                    $endHours = (int) $endTimeParts[0];
                    $endMinutes = (int) $endTimeParts[1];
                    $endSeconds = 0;
                }

                $endTime = Carbon::create($request->event_year, $request->event_month, $actualEndDay, $endHours, $endMinutes, $endSeconds);
            }

            $event->start_time = $startTime;
            $event->end_time = $endTime;
        }

        // 4. Save to database
        $event->save();

        return response()->json([
            'status' => 'success', 
            'message' => 'Event updated successfully',
            'event' => $event
        ]);
    }

    public function destroy($id)
    {
        // 1. Find the exact event
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }

        // 2. Delete it
        $event->delete();

        return response()->json([
            'status' => 'success', 
            'message' => 'Event deleted successfully'
        ]);
    }
}