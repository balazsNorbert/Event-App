<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'location', 'start_time', 'end_time', 'user_id', 'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rsvps()
    {
        return $this->hasMany(Rsvp::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'rsvps')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
