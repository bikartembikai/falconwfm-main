<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilitatorController extends Controller
{
    // Dashboard: Event List
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) return redirect('/login');

        // Ensure user is facilitator
        if ($user->role !== 'facilitator') {
            return redirect('/')->with('error', 'Access denied.');
        }

        // Fetch all upcoming events for the "Event List" (Marketplace) view
        // The screenshot implies "List of available events"
        $events = \App\Models\Event::where('status', 'upcoming')
                    ->with('assignments') // to count assigned facilitators
                    ->orderBy('startDateTime', 'asc')
                    ->get();

        $totalEvents = $events->count();

        // Pass variables compatible with the view
        return view('dashboard.facilitator', compact(
            'user', 
            'events',
            'totalEvents'
        ));
    }

    // Show public profile
    public function show($id)
    {
        $facilitator = User::with('skills', 'performanceReviews')->findOrFail($id);
        // Ensure role is facilitator
        if ($facilitator->role !== 'facilitator') {
             abort(404);
        }
        return view('facilitator.show', compact('facilitator'));
    }

    // Edit Profile
    public function edit()
    {
        $user = Auth::user();
        if ($user->role !== 'facilitator') abort(403);
        
        // Load skills
        $user->load('skills');
        
        return view('facilitator.edit', compact('user'));
    }

    // Update Profile
    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'facilitator') abort(403);

        $validated = $request->validate([
            'bankName' => 'nullable|string',
            'bankAccountNumber' => 'nullable|string',
            'phoneNumber' => 'nullable|string',
            'experience' => 'nullable|string',
            'skills' => 'nullable|string', 
        ]);
        
        // Handle skills
        if (isset($validated['skills'])) {
            $skillNames = array_map('trim', explode(',', $validated['skills']));
            $skillIds = [];
            foreach ($skillNames as $name) {
                if (!empty($name)) {
                     $skill = \App\Models\Skill::firstOrCreate(['skillName' => $name]);
                     $skillIds[] = $skill->skillID;
                }
            }
            $user->skills()->sync($skillIds);
            unset($validated['skills']);
        }

        $user->update($validated);

        return redirect()->route('facilitator.dashboard')->with('success', 'Profile updated.');
    }
}

