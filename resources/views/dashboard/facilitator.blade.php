@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-900">My Dashboard</h1>
    <p class="text-slate-600">Welcome back, {{ Auth::user()->name }}</p>
</div>

<!-- My Upcoming Jobs -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between">
        <h3 class="text-lg leading-6 font-medium text-gray-900">My Upcoming Assignments</h3>
        <a href="{{ route('facilitator.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit Profile</a>
    </div>
    <ul role="list" class="divide-y divide-gray-200">
        @forelse($assignments as $assignment)
            <li class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-indigo-600 truncate">{{ $assignment->event->event_name }}</p>
                    <div class="ml-2 flex-shrink-0 flex">
                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $assignment->status ?? 'Confirmed' }}
                        </p>
                    </div>
                </div>
                <div class="mt-2 sm:flex sm:justify-between">
                    <div class="sm:flex">
                        <p class="flex items-center text-sm text-gray-500">
                             {{ $assignment->event->start_date_time->format('M d, Y @ h:i A') }}
                        </p>
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 gap-4">
                        <form action="{{ route('attendance.clockIn') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $assignment->event->id }}">
                             <!-- Simplified for demo: no file input styling -->
                            {{-- <input type="file" name="image_proof" required class="text-xs"> --}}
                            {{-- <button type="submit" class="text-indigo-600 hover:text-indigo-900">Clock In</button> --}}
                            <span class="text-xs">(Clock-in form hidden for demo simplicity)</span>
                        </form>
                        
                        <form action="{{ route('assignment.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Withdraw</button>
                        </form>
                    </div>
                </div>
            </li>
        @empty
            <li class="px-4 py-4 sm:px-6 text-gray-500 text-sm">
                You have no upcoming jobs. check the <a href="{{ route('events.index') }}" class="text-indigo-600">Events Board</a>.
            </li>
        @endforelse
    </ul>
</div>

<!-- Recommendations Upgrade -->
<div class="bg-indigo-50 rounded-lg p-6">
    <h3 class="text-lg font-bold text-indigo-900 mb-2">Recommended for You</h3>
    <p class="text-indigo-700 mb-4">Based on your skills ({{ $facilitator->skills }}) and experience.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
         <!-- This section would dynamically call the service logic. For now linking to debug dashboard. -->
         <p class="text-sm text-indigo-600">
             The system is actively matching you against <strong>{{ \App\Models\Event::count() }}</strong> available events.
             <br>
             <a href="{{ url('/recommender/debug') }}" class="font-bold underline">View Advanced Recommendations Matrix</a>
         </p>
    </div>
</div>
@endsection
