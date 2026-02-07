<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    protected $fillable = [
        'facilitator_id',
        'reviewer_id',
        'event_id',
        'rating',
        'feedback_comments',
        'role',
        'dateSubmitted'
    ];

    protected $casts = [
        'dateSubmitted' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'facilitator_id', 'userID'); // The Reviewee
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id', 'userID'); // The Reviewer
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'eventID');
    }
}
