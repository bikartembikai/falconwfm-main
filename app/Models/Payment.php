<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'paymentID';

    protected $fillable = [
        'assignmentID',
        'title',
        'amount',
        'paymentType',
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
