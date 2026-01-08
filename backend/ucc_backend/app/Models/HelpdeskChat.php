<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpdeskChat extends Model
{
    protected $fillable = ['user_id', 'agent_id', 'status'];

    public function messages() {
        return $this->hasMany(HelpdeskMessage::class, 'chat_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function agent() {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
