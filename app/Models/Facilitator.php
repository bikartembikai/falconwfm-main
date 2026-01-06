<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facilitator extends Model
{
    protected $table = 'facilitator_profile';

    protected $fillable = [
        'user_id',
        'phone_number',
        'address',
        'bio',
        'profile_picture',
        'skills', // Added skills
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
