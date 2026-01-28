<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRule extends Model
{
    // Primary key is string
    protected $primaryKey = 'event_category';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'event_category',
        'required_skills',
        'min_experience',
        'min_rating',
        'intensity_level',
    ];

    protected $casts = [
        'required_skills' => 'array', // Will automatically serialize/unserialize JSON
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'event_category', 'event_category');
    }
}
