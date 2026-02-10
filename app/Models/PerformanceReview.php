<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    protected $primaryKey = 'reviewID';

    protected $fillable = [
        'userID',
        'reviewer_id',
        'event_id',
        'rating',
        'comments',
        'dateSubmitted'
    ];

    protected $casts = [
        'dateSubmitted' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID'); // The Reviewee
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
