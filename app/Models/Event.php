<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'eventID';

    protected $fillable = [
        'eventName', 
        'venue',
        'eventDescription', 
        'eventCategory', 
        'status',
        'quota',
        'startDateTime', 
        'endDateTime', 
        'totalParticipants',
        'remark'
    ];

    protected $casts = [
        'startDateTime' => 'datetime',
        'endDateTime' => 'datetime',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'eventID', 'eventID');
    }

    // Attendance removed, tied to Assignment now via status/clock times

    public function rule()
    {
        return $this->belongsTo(EventRule::class, 'eventCategory', 'eventCategory');
    }
}
