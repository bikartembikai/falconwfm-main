<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'experience',
        'join_date',
        'certifications',
        'average_rating',
        'event_name',
        'venue',
        'event_description',
        'event_category',
        'required_skill_tag',
        'status',
        'quota',
        'start_date_time',
        'end_date_time',
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
