<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Események lista (nyilvános)
     */
    public function index()
    {
        $userId = auth()->id();

        $events = Event::withCount('event_registrations')
            ->orderBy('starts_at')
            ->paginate(6)
            ->through(function ($event) use ($userId) {
                $alreadyJoined = $event->event_registrations()
                ->where('user_id', $userId)
                ->exists();
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'starts_at' => $event->starts_at,
                    'limit' => $event->limit,
                    'image_path' => $event->image_path,
                    'user_id' => $event->user_id,
                    'already_joined' => $alreadyJoined,
                    'is_full' => $event->event_registrations_count >= $event->limit,
                    'reservedCount' => $event->getRegistrations()
                ];
            });

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }

    /**
     * Új esemény létrehozó oldal
     */
    public function create()
    {
        if (! Auth::check()) {
            abort(403);
        }

        return Inertia::render('Events/Create');
    }

    /**
     * Új esemény mentése
     */
    public function store(Request $request)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|min:5',
            'starts_at' => 'required|date|after:now',
            'description' => 'nullable|string|max:5000',
            'limit' => 'required|integer|min:1',
            'image' => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('events', 'public');
        }

        $data['user_id'] = Auth::id();

        Event::create($data);

        return redirect()->route('events.index')
            ->with('success', 'Sikeres mentés!');
    }

    /**
     * Esemény szerkesztése
     */
    public function edit(Event $event)
    {

        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Events/Edit', [
            'event' => $event,
        ]);
    }

    /**
     * Esemény módosítása
     */
    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|min:5',
            'starts_at' => 'required|date|after:now',
            'description' => 'nullable|string|max:5000',
            'limit' => 'required|integer|min:1',
            'image' => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                Storage::disk('public')->delete($event->image_path);
            }

            $data['image_path'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Esemény frissítve!');
    }

    /**
     * Esemény törlése
     */
    public function destroy(Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Esemény törölve!');

    }

    public function signup(Event $event) 
    {
        $user = Auth::user();

        if ($event->event_registrations()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Már jelentkeztél erre az eseményre.');
        }

        DB::transaction(function () use ($event, $user) {

            $event = Event::where('id', $event->id)->lockForUpdate()->first();

            if ($event->event_registrations()->count() >= $event->limit) {
                abort(400, 'Az esemény betelt.');
            }

            $event->event_registrations()->create([
                'user_id' => $user->id,
            ]);

            // Mail::to($event->creator->email)->send(new \App\Mail\NewSignup($user, $event));

            // Mail::to($user->email)->send(new \App\Mail\SignupConfirmation($event));
        });

        return back()->with('success', 'Sikeres jelentkezés!');
    }


}
