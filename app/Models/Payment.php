<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'assignmentID',
        'title',
        'amount',
        'description',
        'paymentStatus',
        'paymentProof',
        'paymentDate'
    ];

    protected $casts = [
        'paymentDate' => 'date',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignmentID', 'assignmentID');
    }
}
