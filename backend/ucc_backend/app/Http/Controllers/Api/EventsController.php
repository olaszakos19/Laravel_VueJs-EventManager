<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;

class EventsController extends Controller
{
    //


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
public function store(StoreEventRequest $request)
{
    $validated = $request->validated();

    $event = Event::create([
        'creator_id' => $request->user()->id,
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'occurence' => $validated['occurence'],
    ]);

    $event->load('creator');

    return response()->json(['event' => $event], 201);
}

public function updateEvent(UpdateEventRequest $request, $id)
{
    $event = Event::findOrFail($id);

    if ($event->creator_id !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validated = $request->validated();

    if (isset($validated['description'])) {
        $event->description = $validated['description'];
    }
    if (isset($validated['title'])) {
        $event->title = $validated['title'];
    }
    if (isset($validated['occurence'])) {
        $event->occurence = $validated['occurence'];
    }

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
