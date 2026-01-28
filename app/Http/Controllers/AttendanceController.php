<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // Show Clock In View
    public function clockin_view()
    {
        $user = Auth::user();
        if (!$user) return redirect('/login');
        
        $facilitator = $user->facilitator;
        if (!$facilitator) return redirect()->route('facilitator.dashboard')->with('error', 'Profile not found');

        // Logic to find "Active Event" (Assigned & Today)
        // Adjust logic based on real date vs simulation
        $today = now()->startOfDay();
        
        // Find assignment for today
        $assignment = \App\Models\Assignment::where('user_id', $user->id)
                            ->whereHas('event', function($q) {
                                $q->whereDate('start_date_time', now()->toDateString());
                            })
                            ->where('status', 'accepted')
                            ->first();
                            
        $activeEvent = $assignment ? $assignment->event : null;

        $currentAttendance = null;
        if ($activeEvent) {
            $currentAttendance = Attendance::where('event_id', $activeEvent->id)
                                    ->where('facilitator_id', $facilitator->id)
                                    ->first();
        }

        $history = Attendance::where('facilitator_id', $facilitator->id)
                             ->with('event')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('facilitator.clockin', compact('activeEvent', 'currentAttendance', 'history'));
    }

    // Clock In
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'image_proof' => 'nullable|image|max:2048', // Made nullable for mobile UI simplicity
        ]);

        $user = Auth::user();
        $facilitator = $user->facilitator;

        if (!$facilitator) return back()->with('error', 'Facilitator profile not found.');

        // Check if already clocked in today/event
        $exists = Attendance::where('event_id', $request->event_id)
                            ->where('facilitator_id', $facilitator->id)
                            ->exists();
        
        if ($exists) {
            return back()->with('error', 'Already clocked in for this event.');
        }

        // Upload Image
        $path = $request->file('image_proof')->store('attendance_proofs', 'public');

        Attendance::create([
            'event_id' => $request->event_id,
            'facilitator_id' => $facilitator->id,
            'clock_in_time' => now(),
            'status' => 'present',
            'image_proof' => $path,
        ]);

        return back()->with('success', 'Clocked In Successfully!');
    }

    // Clock Out
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        
        // Ensure own record
        if ($attendance->facilitator->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $attendance->update([
            'clock_out_time' => now(),
        ]);

        return back()->with('success', 'Clocked Out Successfully!');
    }
}
