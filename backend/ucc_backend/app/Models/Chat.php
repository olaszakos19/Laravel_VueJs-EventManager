<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $fillable = ['user_id', 'agent_id', 'status'];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

        public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
