<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index()
    {
        // Eager load relationships deeply to get Bank Details from Facilitator
        $payments = Payment::with(['attendance.facilitator.user', 'attendance.event'])
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return view('admin.payments', compact('payments'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $payment = Payment::findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            // Store file in 'public/payment_proofs'
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            $payment->update([
                'payment_proof' => $path,
                'payment_status' => 'paid',
                'payment_date' => now(),
            ]);

            return back()->with('success', 'Payment proof uploaded and status updated to PAID.');
        }

        return back()->with('error', 'Please upload a valid file.');
    }
}
