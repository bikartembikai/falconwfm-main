<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facilitator extends Model
{
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

    public function assignments()
    {
        // Assignments are linked via User ID
        return $this->hasMany(Assignment::class, 'user_id', 'user_id');
    }

    public function leaves()
    {
        // Leaves are linked via User ID
        return $this->hasMany(Leave::class, 'user_id', 'user_id');
    }

    public function attendances()
    {
        // Attendances are linked via Facilitator ID
        return $this->hasMany(Attendance::class);
    }

    public function performanceReviews()
    {
        // Performance Reviews are linked via Facilitator ID
        return $this->hasMany(PerformanceReview::class);
    }
}
