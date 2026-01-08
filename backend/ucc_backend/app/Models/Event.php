<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    //
    protected $fillable = [
        'creator_id',
        'title',
        'description',
        'occurence',
    ];

    protected function casts(): array
    {
        return [
            'occurence' => 'datetime'
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'creator_id');
    }
}
