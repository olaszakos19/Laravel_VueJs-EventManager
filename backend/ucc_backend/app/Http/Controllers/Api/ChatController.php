<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Fő chat endpoint
     * POST /api/chat
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        // JWT middleware garantálja
        $user = Auth::user();

        // Aktív chat
        $chat = Chat::firstOrCreate(
            [
                'user_id' => $user->id,
                'status' => 'open',
            ],
            [
                'type' => 'bot',
            ]
        );

        $message = $request->message;
        $text = mb_strtolower($message);

        // User message mentése
        $chat->messages()->create([
            'sender_type' => 'user',
            'message' => $message,
        ]);

        /* =======================
         *  CHAT LEZÁRÁS
         * ======================= */
        if (str_contains($text, 'köszönöm')) {
            $reply = 'Szívesen! 😊 A beszélgetést lezártam.';

            $this->botReply($chat, $reply);

            $chat->update(['status' => 'closed']);

            return response()->json([
                'message' => $reply,
                'closed' => true,
            ]);
        }

        /* =======================
         *  ÜGYINTÉZŐ ÁTADÁS
         * ======================= */
        if (str_contains($text, 'ügyintéző')) {
            $reply = 'Összekapcsolom egy emberi ügyintézővel…';

            $chat->update(['type' => 'agent']);

            $this->botReply($chat, $reply);

            return response()->json([
                'message' => $reply,
                'handover' => true,
            ]);
        }

        /* =======================
         *  LISTÁZÁS
         * ======================= */
        if (str_contains($text, 'listáz') || str_contains($text, 'eseményeim')) {
            return $this->respond($chat, $this->listEvents($user));
        }

        /* =======================
         *  KERESÉS
         * ======================= */
        if (str_contains($text, 'keres')) {
            return $this->respond($chat, $this->searchEventSmart($user, $message));
        }

        /* =======================
         *  TÖRLÉS KÉRÉS
         * ======================= */
        if (str_contains($text, 'törlés')) {
            return $this->respond($chat, $this->requestDeleteEvent($user, $message));
        }

        /* =======================
         *  TÖRLÉS MEGERŐSÍTÉS
         * ======================= */
        if (in_array($text, ['igen', 'nem'])) {
            return $this->respond($chat, $this->confirmDeleteEvent($user, $text));
        }

        /* =======================
         *  KÖSZÖNTÉS
         * ======================= */
        if (
            str_contains($text, 'szia') ||
            str_contains($text, 'hello') ||
            str_contains($text, 'hi')
        ) {
            return $this->respond(
                $chat,
                'Szia! Hogyan segíthetek az eseményeid kezelésében?'
            );
        }

        /* =======================
         *  DEFAULT
         * ======================= */
        return $this->respond(
            $chat,
            'Nem teljesen értem. Szeretnéd, hogy emberi ügyintéző segítsen?'
        );
    }

    /* ============================================================
     *  CHAT HISTORY
     * ============================================================ */

    /**
     * GET /api/chat/history
     */
    public function history()
    {
        $user = Auth::user();

        return response()->json(
            $user->chats()
                ->latest()
                ->get()
                ->map(fn ($chat) => [
                    'id' => $chat->id,
                    'type' => $chat->type,
                    'status' => $chat->status,
                    'created_at' => $chat->created_at->toDateTimeString(),
                    'last_message' => optional(
                        $chat->messages()->latest()->first()
                    )->message,
                ])
        );
    }

    /**
     * GET /api/chat/{chat}/messages
     */
    public function messages(Chat $chat)
    {
        abort_if($chat->user_id !== Auth::id(), 403);

        return response()->json(
            $chat->messages()
                ->orderBy('created_at')
                ->get([
                    'id',
                    'sender_type',
                    'message',
                    'created_at',
                ])
        );
    }

    /* ============================================================
     *  HELPER METÓDUSOK
     * ============================================================ */

    private function respond(Chat $chat, string $message)
    {
        $this->botReply($chat, $message);

        return response()->json(['message' => $message]);
    }

    private function botReply(Chat $chat, string $message)
    {
        $chat->messages()->create([
            'sender_type' => 'bot',
            'message' => $message,
        ]);
    }



    private function listEvents($user): string
    {
        $events = $user->events()->get();

        if ($events->isEmpty()) {
            return 'Jelenleg nincs rögzített eseményed.';
        }

        return "Az eseményeid:\n" .
            $events->map(fn ($e) => "- {$e->title} ({$e->occurrence})")->implode("\n");
    }

    private function searchEventSmart($user, string $message): string
    {
        $query = trim(str_replace([
            'keresd meg', 'keresd', 'keres',
            'találd meg', 'szeretném', 'meg tudod',
            'az', 'a', 'egy', 'eseményt', 'esemény',
        ], '', mb_strtolower($message)));

        if ($query === '') {
            return 'Add meg, melyik eseményt keresed.';
        }

        $events = $user->events()
            ->where('title', 'like', "%{$query}%")
            ->get();

        if ($events->isEmpty()) {
            return "Nem találtam eseményt: \"{$query}\".";
        }

        return "Talált események:\n" .
            $events->map(fn ($e) => "- {$e->title} ({$e->occurrence})")->implode("\n");
    }

    private function requestDeleteEvent($user, string $message): string
    {
        $query = trim(str_replace(
            ['töröld', 'távolítsd el', 'törlés', 'töröld a'],
            '',
            mb_strtolower($message)
        ));

        $event = $user->events()
            ->where('title', 'like', "%{$query}%")
            ->first();

        if (! $event) {
            return 'Nem találtam ilyen eseményt.';
        }

        session(['pending_delete_event_id' => $event->id]);

        return "Biztosan törölni szeretnéd?\n"
            . "{$event->title} ({$event->occurrence})\n"
            . 'Írd: IGEN / NEM';
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

        $user->events()
            ->whereKey(session()->pull('pending_delete_event_id'))
            ->delete();

        return 'Az esemény törölve lett.';
    }
}
