@extends('layouts.dashboard')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-slate-800">Create New Event</h2>

    <form action="{{ route('events.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700">Event Name</label>
            <input type="text" name="event_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Category</label>
            <select name="event_category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                <option value="Workshop">Workshop</option>
                <option value="Seminar">Seminar</option>
                <option value="Conference">Conference</option>
                <option value="Training">Training</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea name="event_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Venue</label>
            <input type="text" name="venue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Required Skills (Comma separated)</label>
            <input type="text" name="required_skill_tag" placeholder="e.g. Leadership, Python, Communication" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Start Date & Time</label>
                <input type="datetime-local" name="start_date_time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">End Date & Time</label>
                <input type="datetime-local" name="end_date_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Quota</label>
            <input type="number" name="quota" min="1" value="10" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Save Event</button>
        </div>
    </form>
</div>
@endsection
