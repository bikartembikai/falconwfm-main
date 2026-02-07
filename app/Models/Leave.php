<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $primaryKey = 'leaveID';

    protected $fillable = [
        'userID',
        'startDate',
        'endDate',
        'status',
        'reason'
    ];

    protected $casts = [
        'startDate' => 'date',
        'endDate' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }
}
