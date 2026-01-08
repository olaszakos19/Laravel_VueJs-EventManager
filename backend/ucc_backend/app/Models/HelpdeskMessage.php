<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpdeskMessage extends Model
{
    protected $fillable = ['chat_id', 'sender_id', 'sender_type', 'message'];

    public function chat() {
        return $this->belongsTo(HelpdeskChat::class, 'chat_id');
    }
}