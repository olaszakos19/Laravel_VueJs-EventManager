<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_id',
        'sender_type',
        'message',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function messages()
    {
        return $this->belonsgTo(Chat::class);
    }
}
