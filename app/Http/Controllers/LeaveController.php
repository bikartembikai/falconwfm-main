<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $leaves = Leave::where('userID', $user->userID)->orderBy('created_at', 'desc')->get();
        return view('facilitator.leaves', compact('leaves'));
    }

    // Admin: View all leave requests
    public function adminIndex()
    {
        $leaves = Leave::with('user')->orderBy('created_at', 'desc')->get();

        $totalLeaves = $leaves->count();
        $approvedLeaves = $leaves->where('status', 'approved')->count();
        $pendingLeaves = $leaves->where('status', 'pending')->count();
        $rejectedLeaves = $leaves->where('status', 'rejected')->count();

        return view('admin.leaves', compact(
            'leaves',
            'totalLeaves',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date', // Keeping request key as snake_case for HTML standard, or change view? View uses name="start_date".
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        Leave::create([
            'userID' => Auth::user()->userID,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Leave request submitted.');
    }

    // Admin: Update leave status
    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $leave->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Leave status updated.');
    }

    // Admin: Delete leave request
    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->delete();

        return back()->with('success', 'Leave request deleted.');
    }
}

