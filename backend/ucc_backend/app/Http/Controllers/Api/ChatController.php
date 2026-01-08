<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'message' => 'A chat használatához be kell jelentkezned. Kérlek jelentkezz be, majd próbáld újra.',
            ], 401);
        }

        $message = $request->message;
        $userMessage = mb_strtolower($message);
        
        if (str_contains($userMessage, 'ügyintéző')) {
            return response()->json([
                'message' => 'Összekapcsolom egy emberi ügyfélszolgálati munkatárssal… Kérlek várj egy pillanatot.',
                'handover' => true,
            ]);
        }

        if (
            str_contains($userMessage, 'listáz') ||
            str_contains($userMessage, 'eseményeim')
        ) {
            return response()->json([
                'message' => $this->listEvents($user),
            ]);
        }

        if (str_contains($userMessage, 'keres')) {
            return response()->json([
                'message' => $this->searchEventSmart($user, $message),
            ]);
        }

        if (str_contains($userMessage, 'törlés')) {
            return response()->json([
                'message' => $this->requestDeleteEvent($user, $message),
            ]);
        }

        if (in_array($userMessage, ['igen', 'nem'])) {
            return response()->json([
                'message' => $this->confirmDeleteEvent($user, $userMessage),
            ]);
        }

        if (
            str_contains($userMessage, 'hello') ||
            str_contains($userMessage, 'hi') ||
            str_contains($userMessage, 'szia')
        ) {
            return response()->json([
                'message' => 'Szia! Hogyan segíthetek az eseményeid kezelésében?',
            ]);
        }

        return response()->json([
            'message' => 'Sajnálom, nem értem a kérdést. Szeretnéd, ha kapcsolnánk egy emberi ügyfélszolgálathoz?',
        ]);
    }

    private function listEvents($user): string
    {
        $events = $user->events;

        if ($events->isEmpty()) {
            return 'Jelenleg nincs rögzített eseményed.';
        }

        $response = "Az eseményeid:\n";

        foreach ($events as $event) {
            $response .= "- {$event->title} ({$event->occurrence})\n";
        }

        return $response;
    }

    private function searchEventSmart($user, string $message): string
    {
        $text = mb_strtolower($message);

        $remove = [
            'keresd meg', 'keresd', 'keres',
            'találd meg', 'szeretném', 'meg tudod',
            'az', 'a', 'egy', 'eseményt', 'esemény',
        ];

        $query = trim(str_replace($remove, '', $text));

        if ($query === '') {
            return 'Kérlek add meg, melyik eseményt keresed. Pl: "keresd meg a meeting eseményt"';
        }

        $events = $user->events()
            ->where('title', 'like', "%{$query}%")
            ->get();

        if ($events->isEmpty()) {
            return "Nem találtam eseményt a következő névvel: \"{$query}\".";
        }

        $response = "Talált események:\n";

        foreach ($events as $event) {
            $response .= "- {$event->title} ({$event->occurrence})\n";
        }

        return $response;
    }

    private function requestDeleteEvent($user, string $message): string
    {
        $text = mb_strtolower($message);

        $remove = [
            'töröld', 'távolítsd el', 'töröld a', 'törlés',
        ];

        $query = trim(str_replace($remove, '', $text));

        $event = $user->events()
            ->where('title', 'like', "%{$query}%")
            ->first();

        if (! $event) {
            return 'Nem találtam ilyen eseményt.';
        }

        session([
            'pending_delete_event_id' => $event->id,
        ]);

        return "Biztosan törölni szeretnéd ezt az eseményt?\n"
             ."{$event->title} ({$event->occurrence})\n"
             .'Írd: IGEN / NEM';
    }

    private function confirmDeleteEvent($user, string $answer): string
    {
        if (! session()->has('pending_delete_event_id')) {
            return 'Nincs folyamatban törlés.';
        }

        if ($answer === 'nem') {
            session()->forget('pending_delete_event_id');

            return 'A törlés megszakítva.';
        }

        $eventId = session()->pull('pending_delete_event_id');
        $event = $user->events()->find($eventId);

        if ($event) {
            $event->delete();

            return 'Az esemény sikeresen törölve lett.';
        }

        return 'Hiba történt a törlés során.';
    }
}
