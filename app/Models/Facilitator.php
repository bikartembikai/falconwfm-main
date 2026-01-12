<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facilitator extends Model
{
    protected $table = 'facilitators'; // Fixed table name from facilitator_profile to facilitators

    protected $fillable = [
        'user_id',
        'skills',
        'bank_name',
        'bank_account_number',
        'phone_number',
        'experience',
        'join_date',
        'certifications',
        'average_rating',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function reviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }
}
