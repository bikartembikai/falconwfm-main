@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Event</h1>
        <p class="text-slate-500">Manage and assign facilitators to upcoming events</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Total Events</h3>
            <div class="text-3xl font-bold text-slate-900">{{ $totalEvents }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Pending Assignment</h3>
            <div class="text-3xl font-bold text-yellow-600">{{ $pendingAssignment }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Fully Assigned</h3>
            <div class="text-3xl font-bold text-green-600">{{ $fullyAssigned }}</div>
        </div>
    </div>

    <!-- Tabs / Filters -->
    <div class="bg-transparent border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('events.index') }}" 
               class="{{ !request('status') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                All Events <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium {{ !request('status') ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }}">{{ $totalEvents }}</span>
            </a>

            <a href="{{ route('events.index', ['status' => 'pending']) }}" 
               class="{{ request('status') == 'pending' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Pending Assignment <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium {{ request('status') == 'pending' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }}">{{ $pendingAssignment }}</span>
            </a>

            <a href="{{ route('events.index', ['status' => 'fully_assigned']) }}" 
               class="{{ request('status') == 'fully_assigned' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Fully Assigned <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium {{ request('status') == 'fully_assigned' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }}">{{ $fullyAssigned }}</span>
            </a>
        </nav>
    </div>

    <!-- Event Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
            @php
                $assignedCount = $event->assignments->count();
                $isFullyAssigned = $assignedCount >= $event->quota;
                $statusColor = $isFullyAssigned ? 'green' : 'red'; // Changed from yellow to red/pink as per design screenshot implies urgency or just style
                $statusText = $isFullyAssigned ? 'Fully Assigned' : 'Pending';
                $statusBg = $isFullyAssigned ? 'bg-green-100 text-green-800' : 'bg-red-50 text-red-600'; 
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col h-full hover:shadow-md transition-shadow">
                
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-slate-900 line-clamp-2 leading-tight">{{ $event->event_name }}</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBg }}">
                        {{ $statusText }}
                    </span>
                </div>

                <p class="text-sm text-slate-500 mb-4 line-clamp-3 flex-grow">{{ $event->event_description }}</p>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm text-slate-600">
                         <svg class="flex-shrink-0 mr-2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $event->start_date_time->format('d/m/Y') }}
                    </div>
                    <div class="flex items-center text-sm text-slate-600">
                        <svg class="flex-shrink-0 mr-2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="truncate">{{ $event->venue }}</span>
                    </div>
                    <div class="flex items-center text-sm text-slate-600">
                        <svg class="flex-shrink-0 mr-2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        {{ $assignedCount }} / {{ $event->quota }} facilitators assigned
                    </div>
                </div>

                <!-- Progress Bar? Optional based on design. Let's stick to the "Assign Facilitators" text link style in design -->
                 <!-- Design shows a purple badge for 'capacity' or similar. Let's replicate the bottom action bar -->
                 
                 <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                     <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-700">
                        {{ $event->quota }} capacity
                     </span>
                     
                     <a href="{{ route('assignments.create', $event->id) }}" class="text-sm font-semibold text-slate-900 hover:text-indigo-600 flex items-center transition-colors">
                         Assign Facilitators
                         <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                     </a>
                 </div>

            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-slate-900">No events found</h3>
                <p class="mt-1 text-sm text-slate-500">There are no events matching your filter.</p>
                <div class="mt-6">
                    <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        New Event
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
