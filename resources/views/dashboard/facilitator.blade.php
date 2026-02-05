@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-light text-slate-800">Event List</h1>
        <p class="text-slate-500 mt-1">List of available events</p>
    </div>

    <!-- Stats Row with Create Button -->
    <div class="flex flex-col md:flex-row gap-6 items-stretch">
        <!-- Total Events Card -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 w-full md:w-1/3">
            <h3 class="text-sm font-medium text-slate-500 mb-4">Total Events</h3>
            <div class="text-4xl font-normal text-slate-900">{{ $totalEvents }}</div>
        </div>

        <!-- Spacer / Right Aligned Actions -->
        <div class="flex-grow flex items-end justify-end">
            <!-- Included as per screenshot even though unusual for facilitator -->
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-slate-900 bg-[#c5f86c] hover:bg-[#b0e655] shadow-sm transition-colors">
                Create &nbsp; <span class="font-bold">Event</span>
            </a>
        </div>
    </div>

    <!-- Event Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($events as $event)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 flex flex-col h-full hover:shadow-md transition-shadow">
            
            <h3 class="text-xl font-normal text-slate-900 mb-4">{{ $event->eventName }}</h3>
            
            <p class="text-sm text-slate-500 mb-6 flex-grow leading-relaxed">
                {{ Str::limit($event->eventDescription, 120) }}
            </p>

            <div class="space-y-4 mb-6">
                 <div class="flex items-center text-sm text-slate-700">
                    <svg class="w-5 h-5 mr-3 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="font-medium">{{ $event->startDateTime->format('d/m/Y') }}</span>
                </div>
                <div class="flex items-center text-sm text-slate-700">
                    <svg class="w-5 h-5 mr-3 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="font-medium truncate">{{ $event->venue }}</span>
                </div>
                <div class="flex items-center text-sm text-slate-700">
                    <svg class="w-5 h-5 mr-3 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-medium">{{ $event->assignments->count() }} / {{ $event->quota }} facilitators assigned</span>
                </div>
            </div>

            <!-- Tags -->
            <div class="flex flex-wrap gap-3 mb-8">
                <!-- Category Tag -->
                <span class="inline-flex items-center px-3 py-1 rounded bg-purple-100 text-purple-900 text-xs font-semibold">
                    {{ $event->eventCategory }}
                </span>
                <!-- Capacity Tag from screenshot -->
                 <span class="inline-flex items-center text-xs text-slate-500 font-medium">
                    {{ $event->quota }} capacity <!-- using quota as capacity proxy -->
                </span>
            </div>

            <div class="mt-auto">
                <a href="{{ route('events.show', $event->eventID) }}" class="group w-full flex items-center justify-between px-4 py-3 border border-slate-100 rounded-lg text-sm font-medium text-slate-900 hover:bg-slate-50 transition-colors">
                    View Event
                    <svg class="w-4 h-4 text-slate-900 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

        </div>
        @empty
        <div class="col-span-full text-center py-12 text-slate-500">
            No events available.
        </div>
        @endforelse
    </div>
</div>
@endsection
