<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('attendance.facilitator.user')->orderBy('created_at', 'desc')->get();
        return view('admin.payments', compact('payments'));
    }
}
