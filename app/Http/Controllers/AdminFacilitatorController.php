<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminFacilitatorController extends Controller
{
    /**
     * Display a list of all facilitators
     */
    public function index()
    {
        $facilitators = User::where('role', 'facilitator')
                           ->withCount('assignments')
                           ->with('skills')
                           ->orderBy('name')
                           ->get();

        $totalFacilitators = $facilitators->count();
        $availableFacilitators = $facilitators->where('availabilityStatus', 'available')->count();
        $busyFacilitators = $facilitators->where('availabilityStatus', 'busy')->count();
        $unavailableFacilitators = $facilitators->where('availabilityStatus', 'unavailable')->count();

        // Get all skill names for suggestions
        $allSkills = Skill::pluck('skillName')->toArray();

        return view('admin.facilitators', compact(
            'facilitators', 
            'totalFacilitators', 
            'availableFacilitators', 
            'busyFacilitators', 
            'unavailableFacilitators',
            'allSkills'
        ));
    }

    /**
     * Store a new facilitator
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'expertise' => 'nullable|string',
            'yearsOfExperience' => 'nullable|integer|min:0',
            'phone' => 'nullable|string',
            'availabilityStatus' => 'nullable|in:available,busy,unavailable',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'facilitator',
            'expertise' => $request->expertise,
            'yearsOfExperience' => $request->yearsOfExperience ?? 0,
            'phone' => $request->phone,
            'availabilityStatus' => $request->availabilityStatus ?? 'available',
            'joinDate' => now(),
        ]);

        return redirect()->route('facilitators.index')->with('success', 'Facilitator added successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $facilitator = User::findOrFail($id);
        return view('admin.facilitator-edit', compact('facilitator'));
    }

    /**
     * Update facilitator
     */
    public function update(Request $request, $id)
    {
        $facilitator = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',userID',
            'expertise' => 'nullable|string',
            'yearsOfExperience' => 'nullable|integer|min:0',
            'phone' => 'nullable|string',
            'availabilityStatus' => 'nullable|in:available,busy,unavailable',
        ]);

        $facilitator->update([
            'name' => $request->name,
            'email' => $request->email,
            'expertise' => $request->expertise,
            'yearsOfExperience' => $request->yearsOfExperience,
            'phone' => $request->phone,
            'availabilityStatus' => $request->availabilityStatus,
        ]);

        if ($request->filled('password')) {
            $facilitator->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('facilitators.index')->with('success', 'Facilitator updated successfully.');
    }

    /**
     * Delete facilitator
     */
    public function destroy($id)
    {
        $facilitator = User::findOrFail($id);
        $facilitator->delete();

        return redirect()->route('facilitators.index')->with('success', 'Facilitator deleted.');
    }
}
