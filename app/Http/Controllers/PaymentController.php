<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    // Admin View: All Payments
    public function index()
    {
        // Eager load relationships: Payment -> Assignment -> (User, Event)
        $payments = Payment::with(['assignment.user', 'assignment.event'])
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return view('admin.payments', compact('payments'));
    }

    // Admin View: Payroll Management
    public function payrollIndex()
    {
        $payments = Payment::with(['assignment.user', 'assignment.event'])
                            ->orderBy('created_at', 'desc')
                            ->get();

        $totalPayments = $payments->count();
        $pendingPayments = $payments->where('paymentStatus', 'pending')->count();
        $approvedPayments = $payments->where('paymentStatus', 'approved')->count();
        $paidPayments = $payments->where('paymentStatus', 'paid')->count();
        $totalAmount = $payments->sum('amount');
        $pendingAmount = $payments->whereIn('paymentStatus', ['pending', 'approved'])->sum('amount');

        return view('admin.payroll', compact(
            'payments',
            'totalPayments',
            'pendingPayments',
            'approvedPayments',
            'paidPayments',
            'totalAmount',
            'pendingAmount'
        ));
    }

    // Admin: Update Payment Status (Approve or Pay with Proof)
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        // Handle Approve action
        if ($request->has('approve')) {
            $payment->update([
                'paymentStatus' => 'approved',
            ]);
            return back()->with('success', 'Payment approved.');
        }

        // Handle Pay action (upload proof)
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            $payment->update([
                'paymentProof' => $path,
                'paymentStatus' => 'paid',
                'paymentDate' => now(),
            ]);

            return back()->with('success', 'Payment proof uploaded and marked as PAID.');
        }

        return back()->with('error', 'Please upload a valid file.');
    }

    // Facilitator View: My Payments/Allowances
    public function facilitatorIndex()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Fetch payments where the associated assignment belongs to the user
        $payments = Payment::whereHas('assignment', function($q) use ($user) {
            $q->where('userID', $user->userID);
        })->with(['assignment.event'])->orderBy('created_at', 'desc')->get();

        // Fetch assignments for the dropdown in "New Request" modal
        $assignments = \App\Models\Assignment::where('userID', $user->userID)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('facilitator.payments', compact('payments', 'assignments'));
    }

    // Facilitator: Request Allowance/Payment
    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,assignmentID',
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'paymentType' => 'required|in:salary,allowance',
            'description' => 'nullable|string',
        ], [
            'assignment_id.required' => 'Please select an event for this payment request.',
            'paymentType.required' => 'Please select a payment type.',
        ]);

        Payment::create([
            'assignmentID' => $request->assignment_id, // Ensure this matches input name
            'title' => $request->title,
            'amount' => $request->amount,
            'paymentType' => $request->paymentType,
            'description' => $request->description,
            'paymentStatus' => 'pending', 
            'paymentDate' => now(),
        ]);

        return back()->with('success', 'Payment request submitted.');
    }
}
