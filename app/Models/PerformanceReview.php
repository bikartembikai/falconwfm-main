<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $primaryKey = 'reviewID';
    
    protected $fillable = [
        'userID',
        'rating',
        'comments',
        'dateSubmitted',
    ];

    protected $casts = [
        'dateSubmitted' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    protected static function booted()
    {
        static::saved(function ($review) {
            $user = $review->user;
            if ($user) {
                // Determine new average
                $avg = $user->reviews()->avg('rating');
                $user->update(['averageRating' => $avg]);
            }
        });

        static::deleted(function ($review) {
            $user = $review->user;
            if ($user) {
                $avg = $user->reviews()->avg('rating') ?? 0;
                $user->update(['averageRating' => $avg]);
            }
        });
    }
}
