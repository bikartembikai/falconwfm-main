<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $primaryKey = 'assignmentID';

    protected $fillable = [
        'eventID',
        'userID',
        // 'role', removed from migration, assumed handled by User role or implication
        'dateAssigned',
        'clockInTime',
        'clockOutTime',
        'status',
        'imageProof',
        'attendanceStatus'
    ];

    protected $casts = [
        'dateAssigned' => 'datetime',
        'clockInTime' => 'datetime',
        'clockOutTime' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'eventID', 'eventID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }
}
