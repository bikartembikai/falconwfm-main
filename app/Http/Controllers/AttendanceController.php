<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
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
        if (!$user) return redirect()->route('login');
        
        // Ensure user is facilitator
        if ($user->role !== 'facilitator') return redirect()->route('dashboard')->with('error', 'Unauthorized');

        // Logic to find "Active Event" (Assigned & Today)
        $today = now()->format('Y-m-d');
        
        // Find assignment for today
        // We look for an assignment where the Event's startDateTime is today
        $activeAssignment = Assignment::where('userID', $user->userID)
                            ->whereHas('event', function($q) use ($today) {
                                $q->whereDate('startDateTime', $today);
                            })
                            // ->where('status', 'accepted') // Verify if status check is needed or if 'assigned' is default
                            ->first();

        // History: Past assignments (completed or just past dates)
        $history = Assignment::where('userID', $user->userID)
                             ->whereNotNull('clockInTime') // Only show where they at least clocked in
                             ->with('event')
                             ->orderBy('dateAssigned', 'desc')
                             ->get();
                             
        // Calculate hours worked for history (simple diff)
        foreach($history as $record) {
            if ($record->clockInTime && $record->clockOutTime) {
                $start = \Carbon\Carbon::parse($record->clockInTime);
                $end = \Carbon\Carbon::parse($record->clockOutTime);
                $record->hours_worked = $start->diffInHours($end) . ' hrs';
            } else {
                $record->hours_worked = '-';
            }
        }

        return view('facilitator.clockin', compact('activeAssignment', 'history'));
    }

    // Clock In
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,eventID',
            'image_proof' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        // Find the assignment
        $assignment = Assignment::where('userID', $user->userID)
                                ->where('eventID', $request->event_id)
                                ->first();

        if (!$assignment) return back()->with('error', 'Assignment not found.');

        // Check if already clocked in
        if ($assignment->clockInTime) {
            return back()->with('error', 'Already clocked in for this event.');
        }

        $path = null;
        if ($request->hasFile('image_proof')) {
            $path = $request->file('image_proof')->store('attendance_proofs', 'public');
        }

        $assignment->update([
            'clockInTime' => now(),
            'attendanceStatus' => 'present',
            'imageProof' => $path,
        ]);

        return back()->with('success', 'Clocked In Successfully!');
    }

    // Clock Out
    public function update(Request $request, $id)
    {
        // $id is likely assignmentID passed from form route
        $assignment = Assignment::findOrFail($id);
        
        // Ensure own record
        if ($assignment->userID !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $assignment->update([
            'clockOutTime' => now(),
            'attendanceStatus' => 'completed', // Or separate status? Migration has 'attendanceStatus'
        ]);

        return back()->with('success', 'Clocked Out Successfully!');
    }
}
