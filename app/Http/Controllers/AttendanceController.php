<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // Clock In
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'image_proof' => 'required|image|max:2048', // 2MB Max
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
