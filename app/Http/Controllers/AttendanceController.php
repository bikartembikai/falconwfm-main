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
                            ->where('status', 'accepted') // Only show accepted assignments
                            ->whereHas('event', function($q) use ($today) {
                                $q->whereDate('startDateTime', $today);
                            })
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
            'attendanceStatus' => 'pending',
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
            // Keep attendanceStatus as 'pending' - admin will verify
        ]);

        return back()->with('success', 'Clocked Out Successfully!');
    }

    /**
     * Upload image proof after clock in
     */
    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'image_proof' => 'required|image|max:5120', // 5MB max
        ]);

        $assignment = Assignment::findOrFail($id);
        
        // Ensure own record
        if ($assignment->userID !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        // Ensure already clocked in
        if (!$assignment->clockInTime) {
            return back()->with('error', 'Please clock in first.');
        }

        $path = $request->file('image_proof')->store('attendance_proofs', 'public');
        
        $assignment->update([
            'imageProof' => $path,
        ]);

        return back()->with('success', 'Proof uploaded successfully!');
    }

    /**
     * Admin: View all attendance records
     */
    public function adminIndex()
    {
        $assignments = Assignment::with(['user', 'event'])
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        $totalRecords = $assignments->count();
        $verifiedRecords = $assignments->where('attendanceStatus', 'verified')->count();
        $pendingRecords = $assignments->where('attendanceStatus', 'pending')->count();
        $absentRecords = $assignments->where('attendanceStatus', 'absent')->count();
        $rejectedRecords = $assignments->where('attendanceStatus', 'rejected')->count();

        return view('admin.attendance', compact(
            'assignments',
            'totalRecords',
            'verifiedRecords',
            'pendingRecords',
            'absentRecords',
            'rejectedRecords'
        ));
    }

    /**
     * Admin: Update attendance status
     */
    public function adminUpdate(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);

        $request->validate([
            'attendanceStatus' => 'required|in:pending,verified,absent,rejected',
        ]);

        if ($request->attendanceStatus === 'verified' && $assignment->attendanceStatus !== 'verified') {
            // Check if payment already exists
            $existingPayment = \App\Models\Payment::where('assignmentID', $assignment->assignmentID)
                                                  ->where('title', 'like', 'Event Completion Fee%')
                                                  ->exists();

            if (!$existingPayment) {
                \App\Models\Payment::create([
                    'assignmentID' => $assignment->assignmentID,
                    'title' => 'Event Completion Fee: ' . ($assignment->event->eventName ?? 'Event'),
                    'amount' => 500.00, // TODO: Retrieve dynamic rate based on event/facilitator
                    'paymentType' => 'salary',
                    'paymentStatus' => 'pending',
                ]);
            }
        }

        $assignment->update([
            'attendanceStatus' => $request->attendanceStatus,
        ]);

        return back()->with('success', 'Attendance status updated.');
    }
}
