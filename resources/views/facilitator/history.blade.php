@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Past Events</h1>
        <p class="text-slate-500 mt-1">View your event history and performance</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Events -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-1">Total Events</h3>
                <div class="text-3xl font-bold text-slate-900">{{ $totalEvents }}</div>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-1">Average Rating</h3>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($avgRating, 1) }}</div>
            </div>
            <div class="p-3 bg-yellow-50 text-yellow-500 rounded-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters (Visual Only) -->
    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
        <div class="relative flex-1">
            <input type="text" placeholder="Search events by name or location..." class="w-full bg-white border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:border-blue-500">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <select class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
            <option>All Types</option>
            <option>Workshop</option>
            <option>Team Building</option>
        </select>
        <select class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
            <option>All Status</option>
            <option>Completed</option>
        </select>
    </div>

    <!-- Event List -->
    <div class="space-y-6">
        @forelse($pastAssignments as $assign)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex flex-col md:flex-row justify-between md:items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $assign->event->eventName }}</h3>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">
                                {{ $assign->event->eventCategory ?? 'Event' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-600 border border-green-100">
                                Completed
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 md:mt-0 flex items-center text-yellow-500">
                        <!-- Shows user's average rating or rating received for this event? Currently shows global average or dummy. 
                             Usually history shows rating RECEIVED for that event. 
                             Assuming we might query for review received for this event later. 
                             For now, let's just show star icon and "Rated" or placeholder if we have per-event rating data.
                             We don't have per-event rating easily available without extra query.
                             Let's just show 'Completed' and maybe a static star if rated. -->
                        <!-- <span class="font-bold text-slate-700 mr-1">4.8</span> 
                        <svg class="w-5 h-5 fill-current" ...> -->
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm text-slate-500 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $assign->event->startDateTime->format('M d, Y') }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $assign->event->venue ?? 'Online/TBD' }}
                    </div>
                    <div class="flex items-center">
                         <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <!-- Duration placeholder. We have startDateTime and endDateTime in Model? -->
                         <!-- Let's calculate duration if endDateTime exists -->
                         @if($assign->event->endDateTime)
                            {{ $assign->event->startDateTime->diffInHours($assign->event->endDateTime) }} Hours
                         @else
                            N/A
                         @endif
                    </div>
                    <div class="flex items-center">
                         <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ $assign->event->totalParticipants }} attendees
                    </div>
                </div>

                <!-- Footer / Feedback Snippet -->
                <!-- <div class="bg-slate-50 rounded-lg p-4 text-sm text-slate-600 italic border border-slate-100">
                    "Excellent facilitation skills and engagement with participants."
                </div> -->
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-500">
                 <svg class="w-12 h-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>No past events found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
