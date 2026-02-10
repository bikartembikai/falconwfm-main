@extends('layouts.dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Back Link -->
    <a href="{{ route('events.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Assignments
    </a>

    <!-- Header & Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col md:flex-row justify-between items-start gap-6">
        <div class="flex-grow">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-2xl font-bold text-gray-900">{{ $event->eventName }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $event->assignments->count() >= $event->quota ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $event->assignments->count() >= $event->quota ? 'Fully Assigned' : 'Pending' }}
                </span>
            </div>
            <p class="text-gray-500 mb-6">{{ $event->eventDescription }}</p>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
                <div>
                    <dt class="text-xs text-gray-400 uppercase font-semibold">Date</dt>
                    <dd class="font-medium text-gray-900 mt-1 flex items-center">
                        <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ $event->startDateTime->format('d/m/Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 uppercase font-semibold">Time</dt>
                    <dd class="font-medium text-gray-900 mt-1 flex items-center">
                         <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $event->startDateTime->format('H:i') }} - {{ $event->endDateTime ? $event->endDateTime->format('H:i') : 'TBD' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 uppercase font-semibold">Location</dt>
                    <dd class="font-medium text-gray-900 mt-1 flex items-center">
                        <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $event->venue }}
                    </dd>
                </div>
                 <div>
                    <dt class="text-xs text-gray-400 uppercase font-semibold">Capacity</dt>
                    <dd class="font-medium text-gray-900 mt-1 flex items-center">
                        <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        {{ $event->totalParticipants ?? 0 }} attendees
                    </dd>
                </div>
            </div>
        </div>

        <!-- Right Side Stats Panel (Similar to Assign Status) -->
        <div class="w-full md:w-80 bg-white border border-blue-200 rounded-lg p-5 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 flex items-center mb-4">
                <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Assignment Status
            </h3>
            
            <div class="mb-2 flex justify-between text-sm font-medium">
                <span class="text-gray-600">Progress</span>
                <span class="text-gray-900">{{ $event->assignments->count() }} / {{ $event->quota }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, ($event->assignments->count() / max(1, $event->quota)) * 100) }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mb-6">
                @if($event->quota > $event->assignments->count())
                    {{ $event->quota - $event->assignments->count() }} more facilitators needed
                @else
                    All positions filled
                @endif
            </p>

            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-1">Category</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $event->eventCategory }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main List: Assign Facilitators -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Assign Facilitators</h2>
                <p class="text-sm text-gray-500 mb-6">Select facilitators to assign to this event</p>

                <form id="assignmentForm" action="{{ route('assignments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->eventID }}">
                    
                    @if($event->assignments->count() >= $event->quota)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    This event has reached its facilitator quota. No further assignments can be made.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                        @foreach($facilitators as $facil)
                        <div class="relative flex items-start p-4 border rounded-lg hover:bg-slate-50 transition-colors {{ $facil['status'] === 'busy' ? 'bg-gray-50 opacity-75' : 'border-gray-200' }}">
                            <div class="flex items-center h-5">
                                <input id="facil_{{ $facil['id'] }}" name="facilitator_ids[]" value="{{ $facil['id'] }}" type="checkbox" 
                                       {{ ($facil['status'] !== 'available' || $event->assignments->count() >= $event->quota) ? 'disabled' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded disabled:opacity-50">
                            </div>
                            <div class="ml-3 flex-grow">
                                <label for="facil_{{ $facil['id'] }}" class="font-medium text-gray-900 {{ $event->assignments->count() >= $event->quota ? 'text-gray-500' : '' }}">{{ $facil['name'] }}</label>
                                <div class="text-sm text-gray-500">
                                    {{ $facil['experience'] }} years experience • Rating: {{ number_format($facil['rating'], 1) }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ Str::limit($facil['skills'], 50) }}
                                </div>
                                @if(isset($facil['match_score']))
                                    <div class="text-xs text-blue-600 font-semibold mt-1">
                                        Match Score: {{ $facil['match_score'] }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-auto flex flex-col items-end">
                                @if($facil['status'] === 'available')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        available
                                    </span>
                                @elseif($facil['status'] === 'busy')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        busy
                                    </span>
                                    <span class="text-[10px] text-red-500 mt-1 max-w-[100px] text-right leading-tight">{{ $facil['reason'] }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        unqualified
                                    </span>
                                    <span class="text-[10px] text-red-500 mt-1 max-w-[100px] text-right leading-tight">{{ $facil['reason'] }}</span>
                                @endif
                            </div>
                        </div>
                        {{-- Anonymous Feedback --}}
                        @if(!empty($facil['feedback']))
                        <div class="ml-7 mt-2 space-y-1">
                            @foreach($facil['feedback'] as $fb)
                            <div class="bg-gray-50 rounded-lg px-3 py-1.5">
                                <p class="text-xs text-gray-600 italic">"{{ Str::limit($fb['comment'], 60) }}"</p>
                                <div class="flex items-center justify-between mt-0.5">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-2.5 h-2.5 {{ $i <= $fb['rating'] ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-[9px] text-gray-400">{{ $fb['date'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-4 flex justify-end">
                        <button type="submit" 
                                {{ $event->assignments->count() >= $event->quota ? 'disabled' : '' }}
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ $event->assignments->count() >= $event->quota ? 'Assignment Closed' : 'Confirm Assignment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar: Smart Recommendation -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-purple-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Smart Recommendation
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Generate recommendations based on facilitator expertise and availability.
                </p>

                <div class="bg-blue-50 rounded-lg p-3 mb-6 flex items-start">
                    <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs text-blue-800">
                        Our AI-powered system analyzes experience, specialization, and availability to recommend the best matches.
                    </p>
                </div>

                <button type="button" onclick="autoSelectRecommended()" 
                        {{ $event->assignments->count() >= $event->quota ? 'disabled' : '' }}
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ $event->assignments->count() >= $event->quota ? 'Event Full' : 'Generate Facilitators' }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function autoSelectRecommended() {
        // Simple logic: Select top N available facilitators based on quota
        const quota = {{ max(1, $event->quota - $event->assignments->count()) }};
        const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(:disabled)');
        
        // Uncheck all first
        document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);

        let count = 0;
        // Checkboxes are already sorted by match_score from Controller/InferenceEngine
        checkboxes.forEach((cb) => {
            if (count < quota) {
                cb.checked = true;
                count++;
            }
        });

        if (count > 0) {
            alert('Top ' + count + ' recommended facilitators selected based on match score.');
        } else {
            alert('No available facilitators found matching criteria.');
        }
    }
</script>
@endsection
