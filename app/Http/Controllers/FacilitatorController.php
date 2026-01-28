<?php

namespace App\Http\Controllers;

use App\Models\Facilitator;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilitatorController extends Controller
{
    // Dashboard: My Jobs, Recommendations
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) return redirect('/login');

        // Ensure user is facilitator
        if ($user->role !== 'facilitator') {
            return redirect('/')->with('error', 'Access denied.');
        }

        $facilitator = $user->facilitator;
        if (!$facilitator) {
            $facilitator = Facilitator::create(['user_id' => $user->id]);
        }

        // Stats Calculation
        $totalAssignments = Assignment::where('user_id', $user->id)->count();
        $pendingAssignments = Assignment::where('user_id', $user->id)->where('status', 'pending')->count();
        
        // Calculate Hours Worked (Sum of completed attendance hours)
        $attendances = \App\Models\Attendance::where('facilitator_id', $facilitator->id)
                            ->whereNotNull('clock_out_time')
                            ->get();
        $hoursWorked = 0;
        foreach ($attendances as $att) {
            $start = \Carbon\Carbon::parse($att->clock_in_time);
            $end = \Carbon\Carbon::parse($att->clock_out_time);
            $hoursWorked += $end->diffInHours($start); // or float diffInMinutes / 60
        }
        $hoursWorked = number_format($hoursWorked, 1) . 'h';

        // Pending Allowance (Sum of 'pending' payments linked to attendance)
        // Adjust logic if Payment logic differs, assuming Payment linked to Attendance
        $pendingAllowance = \App\Models\Payment::whereHas('attendance', function($q) use ($facilitator) {
                                $q->where('facilitator_id', $facilitator->id);
                            })
                            ->where('payment_status', 'pending')
                            ->sum('amount');
        $pendingAllowance = 'RM' . number_format($pendingAllowance, 0);

        $assignments = Assignment::where('user_id', $user->id)
                                 ->with('event')
                                 ->orderBy('date_assigned', 'desc')
                                 ->take(5) // Limit for Recent list
                                 ->get();

        return view('dashboard.facilitator', compact(
            'facilitator', 
            'assignments',
            'totalAssignments',
            'pendingAssignments',
            'hoursWorked',
            'pendingAllowance'
        ));
    }

    // Show public profile
    public function show($id)
    {
        $facilitator = Facilitator::with('user', 'reviews')->findOrFail($id);
        return view('facilitator.show', compact('facilitator'));
    }

    // Edit Profile
    public function edit()
    {
        $user = Auth::user();
        $facilitator = $user->facilitator;
        return view('facilitator.edit', compact('facilitator'));
    }

    // Update Profile
    public function update(Request $request)
    {
        $user = Auth::user();
        $facilitator = $user->facilitator;

        $validated = $request->validate([
            'skills' => 'nullable|string',
            'experience' => 'nullable|string',
            'certifications' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'join_date' => 'nullable|date',
        ]);

        if (!$facilitator) {
            $validated['user_id'] = $user->id;
            Facilitator::create($validated);
        } else {
            $facilitator->update($validated);
        }

        return redirect()->route('facilitator.dashboard')->with('success', 'Profile updated.');
    }
}
