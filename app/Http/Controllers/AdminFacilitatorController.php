<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminFacilitatorController extends Controller
{
    /**
     * Display a list of all facilitators
     */
    public function index()
    {
        $today = Carbon::today();

        $facilitators = User::where('role', 'facilitator')
                           ->withCount('assignments')
                           ->with(['skills', 'reviews', 'leaves', 'assignments.event'])
                           ->orderBy('name')
                           ->get();

        // Compute dynamic status for each facilitator
        foreach ($facilitators as $facilitator) {
            // Check for approved leave overlapping today
            $activeLeave = $facilitator->leaves
                ->where('status', 'approved')
                ->filter(function ($leave) use ($today) {
                    return $leave->startDate <= $today && $leave->endDate >= $today;
                })
                ->first();

            if ($activeLeave) {
                $facilitator->dynamicStatus = 'unavailable';
                $facilitator->statusReason = 'On leave until ' . $activeLeave->endDate->format('d M Y');
                continue;
            }

            // Check for assignment with event overlapping today
            $activeAssignment = $facilitator->assignments
                ->filter(function ($assignment) use ($today) {
                    $event = $assignment->event;
                    if (!$event) return false;
                    return $event->startDateTime->startOfDay() <= $today && $event->endDateTime->endOfDay() >= $today;
                })
                ->first();

            if ($activeAssignment) {
                $facilitator->dynamicStatus = 'busy';
                $facilitator->statusReason = $activeAssignment->event->eventName;
                continue;
            }

            $facilitator->dynamicStatus = 'available';
            $facilitator->statusReason = null;
        }

        $totalFacilitators = $facilitators->count();
        $availableFacilitators = $facilitators->where('dynamicStatus', 'available')->count();
        $busyFacilitators = $facilitators->where('dynamicStatus', 'busy')->count();
        $unavailableFacilitators = $facilitators->where('dynamicStatus', 'unavailable')->count();

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
            'yearsOfExperience' => 'nullable|integer|min:0',
            'phone' => 'nullable|string',
            'skills' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'facilitator',
            'experience' => $request->yearsOfExperience ? $request->yearsOfExperience . ' years' : null,
            'phoneNumber' => $request->phone,
            'joinDate' => now(),
        ]);

        // Sync skills
        if ($request->filled('skills')) {
            $skillNames = array_filter(explode(',', $request->skills));
            $skillIds = [];
            foreach ($skillNames as $skillName) {
                $skill = Skill::firstOrCreate(['skillName' => trim($skillName)]);
                $skillIds[] = $skill->skillID;
            }
            $user->skills()->sync($skillIds);
        }

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
            'yearsOfExperience' => 'nullable|integer|min:0',
            'phone' => 'nullable|string',
            'skills' => 'nullable|string',
        ]);

        $facilitator->update([
            'name' => $request->name,
            'email' => $request->email,
            'experience' => $request->yearsOfExperience ? $request->yearsOfExperience . ' years' : null,
            'phoneNumber' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $facilitator->update(['password' => Hash::make($request->password)]);
        }

        // Sync skills
        if ($request->filled('skills')) {
            $skillNames = array_filter(explode(',', $request->skills));
            $skillIds = [];
            foreach ($skillNames as $skillName) {
                $skill = Skill::firstOrCreate(['skillName' => trim($skillName)]);
                $skillIds[] = $skill->skillID;
            }
            $facilitator->skills()->sync($skillIds);
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
