<?php

namespace App\Http\Controllers;

use App\Models\EventRule;
use Illuminate\Http\Request;

class EventRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rules = EventRule::all();
        return view('event_rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('event_rules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_category' => 'required|string|unique:event_rules,event_category',
            'required_skills' => 'nullable|string', // Comma separated input
            'required_specialization' => 'nullable|string',
            'min_experience' => 'required|integer|min:0',
            'min_rating' => 'required|integer|min:0|max:5',
        ]);

        // Process required_skills from comma-separated string to array
        if (!empty($validated['required_skills'])) {
            $skills = array_map('trim', explode(',', $validated['required_skills']));
            $validated['required_skills'] = $skills;
        } else {
            $validated['required_skills'] = [];
        }

        EventRule::create($validated);

        return redirect()->route('event-rules.index')->with('success', 'Event Rule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventRule $eventRule)
    {
        return view('event_rules.show', compact('eventRule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($event_category)
    {
        $eventRule = EventRule::findOrFail($event_category);
        return view('event_rules.edit', compact('eventRule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $event_category)
    {
        $eventRule = EventRule::findOrFail($event_category);

        $validated = $request->validate([
            'required_skills' => 'nullable|string',
            'required_specialization' => 'nullable|string',
            'min_experience' => 'required|integer|min:0',
            'min_rating' => 'required|integer|min:0|max:5',
        ]);

        if (isset($validated['required_skills'])) {
            $skills = array_map('trim', explode(',', $validated['required_skills']));
            $validated['required_skills'] = $skills;
        }

        $eventRule->update($validated);

        return redirect()->route('event-rules.index')->with('success', 'Event Rule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($event_category)
    {
        $eventRule = EventRule::findOrFail($event_category);
        $eventRule->delete();

        return redirect()->route('event-rules.index')->with('success', 'Event Rule deleted successfully.');
    }
}
