<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'starts_at',
        'limit',
        'image_path'
    ];

    protected $dates = ['starts_at'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event_registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function getRegistrations()
    {
        return $this->event_registrations()->count();
    }
}
