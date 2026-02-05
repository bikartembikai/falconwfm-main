<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $primaryKey = 'skillID';

    protected $fillable = ['skillName'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'facilitator_skills', 'skillID', 'userID');
    }
}
