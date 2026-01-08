<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{
    //
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'occurence' => 'required|date',
        ]);

        $event = Event::create([
            'creator_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'occurence' => $validated['occurence'],
        ]);

        $event->load('creator');

        return response()->json([
            'event' => $event,
        ], 201);

    }

    public function getEvents()
    {
        $events = Event::with('creator')->get();

        return response()->json($events);
    }

    public function getEvent($id)
    {
        $event = Event::find($id);

        return response()->json($event);
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if ($event->creator_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'description' => 'nullable|string|max:255',
        ]);

        $event->description = $request->description;
        $event->save();

        return response()->json($event, 201);
    }

    public function deleteEvent($id)
    {
        $event = Event::findOrFail($id);

        if ($event->creator_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Esemény törölve']);
    }
}
