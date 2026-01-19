<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'event_id',
        'facilitator_id',
        'clock_in_time',
        'clock_out_time',
        'status',
        'image_proof'
    ];

    protected $casts = [
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
