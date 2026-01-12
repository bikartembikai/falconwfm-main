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
            // Auto-create profile if missing (simplified flow)
            $facilitator = Facilitator::create(['user_id' => $user->id]);
        }

        $assignments = Assignment::where('user_id', $user->id)
                                 ->with('event')
                                 ->orderBy('date_assigned', 'desc')
                                 ->get();

        return view('dashboard.facilitator', compact('facilitator', 'assignments'));
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
