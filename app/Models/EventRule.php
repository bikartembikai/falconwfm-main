<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRule extends Model
{
    use HasFactory;

    protected $primaryKey = 'eventCategory';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'eventCategory',
        'requiredSkill', // Singular as per diagram
        // 'requiredSpecialization', // Removed/merged? Diagram says requiredSkill. Let's keep strict to diagram if specific.
        // Wait, migration I kept requiredSpecialization? No, I commented "diagram only shows requiredSkill".
        // Let's stick to migration which kept 'requiredSkill' text.
         'minExperience',
         'minRating',
    ];

    protected $casts = [
        'requiredSkill' => 'array', // Assuming logic still wants array even if singular name
    ];
}
