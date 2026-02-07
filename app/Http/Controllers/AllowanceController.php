<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class AllowanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch payments (allowances) where the associated assignment belongs to the user
        $allowances = Payment::whereHas('assignment', function($q) use ($user) {
            $q->where('userID', $user->userID);
        })->orderBy('created_at', 'desc')->get();

        // Fetch assignments for the dropdown in "New Request" modal
        $assignments = \App\Models\Assignment::where('userID', $user->userID)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('facilitator.allowances', compact('allowances', 'assignments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,assignmentID', // Adjusted to assignmentID based on context
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        Payment::create([
            'assignmentID' => $request->assignment_id,
            'title' => $request->title,
            'amount' => $request->amount,
            'description' => $request->description,
            'paymentStatus' => 'pending', // Default status
            'paymentDate' => now(),
        ]);

        return back()->with('success', 'Allowance request submitted.');
    }
}
