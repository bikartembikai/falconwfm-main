<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Assignment;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Facilitator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilitatorController extends Controller
{
    // Dashboard: Facilitator Portal
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) return redirect('/login');

        // Ensure user is facilitator
        if ($user->role !== 'facilitator') {
            return redirect('/')->with('error', 'Access denied.');
        }

        // 1. Stats
        $totalAssignments = Assignment::where('userID', $user->userID)->count();
        $pendingResponse = Assignment::where('userID', $user->userID)->where('status', 'pending')->count();
        
        // "Pending Allowance" - proxying via Payments linked to this user's assignment
        // Assuming relation User -> assignments -> payment exists
        // Payment belongs directly to Assignment
        $pendingAllowance = Payment::whereHas('assignment', function($q) use ($user) {
            $q->where('userID', $user->userID);
        })->where('paymentStatus', 'pending')->count(); // Note: DB column is paymentStatus (camelCase) based on migration? Check migration. 
        // Migration says: 'paymentStatus' default 'pending'. Model fillable says 'payment_status'. 
        // Let's check model fillable again or stick to migration column name if known. 
        // Migration: $table->string('paymentStatus')->default('pending');
        // Let's use 'paymentStatus' (camelCase) to be safe with Schema, or check if specific cast exists.

        // 2. Recent Assignments
        $recentAssignments = Assignment::where('userID', $user->userID)
                                ->with('event')
                                ->orderBy('dateAssigned', 'desc')
                                ->take(5)
                                ->get();

        // 3. Recent Allowance Requests (Pending Payments)
        $allowanceRequests = Payment::whereHas('assignment', function($q) use ($user) {
            $q->where('userID', $user->userID);
        })
        ->with('assignment.event')
        ->where('paymentStatus', 'pending')
        ->orderBy('created_at', 'desc') 
        ->take(5)
        ->get();

        return view('dashboard.facilitator', compact(
            'user', 
            'totalAssignments',
            'pendingResponse',
            'pendingAllowance',
            'recentAssignments',
            'allowanceRequests'
        ));
    }

    // Performance Reviews Page
    public function reviews()
    {
        $user = Auth::user();
        if ($user->role !== 'facilitator') abort(403);

        // 1. Get my assignments for completed events
        // Using whereHas to filter assignments by event status
        $myAssignments = Assignment::where('userID', $user->userID)
            ->whereHas('event', function($q) {
                // Logic: Events that are finished. Checking status 'completed'
                $q->where('status', 'completed'); 
            })
            ->pluck('eventID'); // Get list of Event IDs
            
        // 2. Get Co-facilitators (Potential Reviews)
        // Find all assignments for these events excluding myself
        $coAssignments = Assignment::whereIn('eventID', $myAssignments)
            ->where('userID', '!=', $user->userID)
            ->with(['user', 'event'])
            ->get();
            
        // 3. Get reviews I have already written
        // We need to match by (event_id, facilitator_id)
        $myReviews = \App\Models\PerformanceReview::where('reviewer_id', $user->userID)->get();
        
        // Map for quick lookup: "eventID_userID"
        $myReviewsMap = [];
        foreach($myReviews as $review) {
            $myReviewsMap[$review->event_id . '_' . $review->facilitator_id] = $review;
        }
        
        $pendingReviews = [];
        $completedReviewsList = [];
        
        foreach($coAssignments as $assign) {
            $key = $assign->eventID . '_' . $assign->userID;
            
            // Avoid duplicate entries if multiple assignments exist? (Unlikely for same user/event)
            // But let's verify uniqueness in list if needed. For now assuming 1 assignment per user per event.
            
            if (isset($myReviewsMap[$key])) {
                // Attach the review object to assignment for display
                $assign->review = $myReviewsMap[$key];
                $completedReviewsList[] = $assign;
            } else {
                $pendingReviews[] = $assign;
            }
        }
        
        $totalReviews = count($completedReviewsList) + count($pendingReviews);
        $pendingCount = count($pendingReviews);
        $completedCount = count($completedReviewsList);

        return view('facilitator.reviews', compact('user', 'pendingReviews', 'completedReviewsList', 'totalReviews', 'pendingCount', 'completedCount'));
    }

    // Past Events History
    public function history()
    {
        $user = Auth::user();
        if ($user->role !== 'facilitator') abort(403);
        
        $pastAssignments = Assignment::where('userID', $user->userID)
            ->whereHas('event', function($q) {
                $q->where('status', 'completed');
            })
            ->with('event')
            ->get()
            ->sortByDesc(function($assignment) {
                return $assignment->event->startDateTime;
            });
            
        // Stats
        $totalEvents = $pastAssignments->count();
        $avgRating = $user->averageRating ?? 0;
        
        return view('facilitator.history', compact('user', 'pastAssignments', 'totalEvents', 'avgRating'));
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

    // My Profile (Read Only)
    public function profile()
    {
        $user = Auth::user();
        if ($user->role !== 'facilitator') abort(403);
        
        $user->load('skills');
        return view('facilitator.profile', compact('user'));
    }

    // Edit Profile View
    public function editProfile()
    {
        $user = Auth::user();
        if ($user->role !== 'facilitator') abort(403);
        
        $user->load('skills');
        return view('facilitator.profile-edit', compact('user'));
    }

    // Handle Profile Update
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'facilitator') abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->userID . ',userID', // Exclude current user
            'phoneNumber' => 'nullable|string|max:20',
            'experience' => 'nullable|string', // Years of experience
            'bankName' => 'nullable|string',
            'bankAccountNumber' => 'nullable|string',
            'skills' => 'nullable|string', // Comma separated
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

        return redirect()->route('facilitator.profile')->with('success', 'Profile updated successfully.');
    }
}

