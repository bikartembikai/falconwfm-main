<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    protected $fillable = [
        'facilitator_id',
        'rating',
        'feedback_comments',
        'role',
        'date_submitted'
    ];

    protected $casts = [
        'date_submitted' => 'date',
    ];

    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
}
