@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header / Title -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">{{ $event->eventName }}</h1>
            <div class="mt-2 flex items-center space-x-4 text-sm text-slate-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-blue-100 text-blue-800">
                    {{ $event->eventCategory }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium {{ $event->status == 'completed' ? 'bg-green-100 text-green-800' : ($event->status == 'ongoing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ ucfirst($event->status) }}
                </span>
            </div>
        </div>
        <div class="text-right">
             <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; Back to Assignments
            </a>
        </div>
    </div>

    <!-- Main Details Card -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-slate-200">
        <div class="px-4 py-5 sm:px-6 bg-slate-50 border-b border-slate-200">
            <h3 class="text-lg leading-6 font-medium text-slate-900">Event Details</h3>
            <p class="mt-1 max-w-2xl text-sm text-slate-500">Comprehensive information about the event.</p>
        </div>
        <div class="border-t border-slate-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-slate-200">
                <!-- Description -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-slate-500">Description</dt>
                    <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2">
                        {{ $event->eventDescription }}
                    </dd>
                </div>
                
                <!-- Date & Time -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-slate-500">Date & Time</dt>
                    <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2">
                        <div class="flex flex-col">
                            <span class="font-semibold">{{ $event->startDateTime->format('l, d F Y') }}</span>
                            <span>{{ $event->startDateTime->format('h:i A') }} - {{ $event->endDateTime ? $event->endDateTime->format('h:i A') : 'TBD' }}</span>
                        </div>
                    </dd>
                </div>

                <!-- Venue -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-slate-500">Venue</dt>
                    <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2">
                        {{ $event->venue }}
                    </dd>
                </div>

                <!-- Participants Info -->
                 <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-slate-500">Participation</dt>
                    <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2 flex gap-8">
                        <div>
                            <span class="block text-xs text-slate-400 uppercase font-bold">Total Participants</span>
                            <span class="block text-lg font-medium">{{ $event->totalParticipants ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-400 uppercase font-bold">Facilitator Quota</span>
                            <span class="block text-lg font-medium">{{ $event->quota }}</span>
                        </div>
                    </dd>
                </div>

                <!-- Required Skills -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-slate-500">Required Skills</dt>
                    <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2">
                        <div class="flex flex-wrap gap-2">
                            @if($event->requiredSkills && is_array($event->requiredSkills))
                                @foreach($event->requiredSkills as $skill)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-slate-500 italic">No specific skills listed.</span>
                            @endif
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Assigned Facilitators Section -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-slate-200">
        <div class="px-4 py-5 sm:px-6 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-slate-900">Facilitator Assignments</h3>
                <p class="mt-1 max-w-2xl text-sm text-slate-500">Current status of assigned facilitators.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $event->assignments->where('status', 'accepted')->count() >= $event->quota ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                {{ $event->assignments->where('status', 'accepted')->count() }} / {{ $event->quota }} Filled
            </span>
        </div>
        <div class="border-t border-slate-200">
            @if($event->assignments->count() > 0)
            <ul class="divide-y divide-slate-200">
                @foreach($event->assignments as $assignment)
                <li class="px-4 py-4 sm:px-6 flex items-center justify-between hover:bg-slate-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold">
                            {{ substr($assignment->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-slate-900">{{ $assignment->user->name ?? 'Unknown Facilitator' }}</h4>
                            <p class="text-xs text-slate-500">{{ $assignment->user->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->status == 'accepted' ? 'bg-green-100 text-green-800' : ($assignment->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                        
                        <form action="{{ route('assignments.destroy', $assignment->assignmentID) }}" method="POST" onsubmit="return confirm('Are you sure you want to unassign this facilitator?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Unassign">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="p-6 text-center text-slate-500 italic">
                No facilitators have been assigned yet.
            </div>
            @endif
        </div>
        <div class="px-4 py-4 sm:px-6 bg-slate-50 border-t border-slate-200">
            <a href="{{ route('assignments.create', $event->eventID) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Manage Assignments
            </a>
        </div>
    </div>
</div>
@endsection
