<?php

namespace App\Http\Controllers;

use App\Models\EventFilterView;
use Illuminate\Http\Request;
// use App\Models\Event; // <-- You will need your regular Event model to create/update/delete

class EventController extends Controller
{
    /**
     * Fetch and filter events for the Vue Calendar (Matches Route::get('/api/events'))
     */
    public function index(Request $request)
    {
        $query = EventFilterView::query();

        // Filter by Month
        if ($request->filled('month')) {
            $query->where('event_month', $request->month);
        }

        // Filter by Year
        if ($request->filled('year')) {
            $query->where('event_year', $request->year);
        }

        // Filter by Title
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        $events = $query->get();

        // Return as JSON so Vue can read it!
        return response()->json([
            'status' => 'success',
            'events' => $events
        ]);
    }

    /**
     * Store a newly created event (Matches Route::post('/api/events'))
     */
    public function store(Request $request)
    {
        // Add your validation and event creation logic here later
        // Example: Event::create($request->all());
        
        return response()->json(['status' => 'success', 'message' => 'Event created successfully']);
    }

    /**
     * Update the specified event (Matches Route::put('/api/events/{id}'))
     */
    public function update(Request $request, $id)
    {
        // Add your validation and event update logic here later
        
        return response()->json(['status' => 'success', 'message' => 'Event updated successfully']);
    }

    /**
     * Remove the specified event (Matches Route::delete('/api/events/{id}'))
     */
    public function destroy($id)
    {
        // Add your event deletion logic here later
        
        return response()->json(['status' => 'success', 'message' => 'Event deleted successfully']);
    }
}