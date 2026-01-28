@extends('layouts.app')

@section('content')
<div class="flex h-screen overflow-hidden bg-gray-100">
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                
                <!-- Back Button -->
                <div class="mb-4">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Events
                    </a>
                </div>

                <div class="flex gap-6">
                    <!-- Left Column: Event Details & Facilitator List -->
                    <div class="flex-1 space-y-6">
                        
                        <!-- Event Details Card -->
                        <div class="bg-white rounded-lg shadow-sm p-6 relative overflow-hidden">
                             <!-- Status Badge -->
                             <span class="absolute top-6 right-6 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200 uppercase">
                                {{ $event->status }}
                            </span>

                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $event->event_name }}</h1>
                            <p class="text-gray-500 mb-6">{{ $event->event_description }}</p>

                            <div class="grid grid-cols-2 gap-y-4 text-sm">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-3 text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Date</p>
                                        <p class="text-gray-500">{{ $event->start_date_time->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-3 text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Time</p>
                                        <p class="text-gray-500">{{ $event->start_date_time->format('H:i') }} - {{ $event->end_date_time ? $event->end_date_time->format('H:i') : 'TBD' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-3 text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Location</p>
                                        <p class="text-gray-500">{{ $event->venue }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-3 text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Capacity</p>
                                        <p class="text-gray-500">{{ $event->quota }} (Target)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assign Facilitators Section -->
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-lg font-medium text-gray-900">Assign Facilitators</h3>
                                <p class="text-sm text-gray-500">Select facilitators to assign to this event. (Sorted by Best Match)</p>
                            </div>
                            
                            <form action="{{ route('assignments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">

                                <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                                    @foreach($facilitators as $facil)
                                    <div class="p-4 flex items-center hover:bg-gray-50 transition-colors {{ $facil['status'] !== 'available' ? 'opacity-75 bg-slate-50' : '' }}">
                                        <!-- Checkbox -->
                                        <div class="flex items-center h-5">
                                            <input id="facil_{{ $facil['id'] }}" name="facilitator_ids[]" value="{{ $facil['id'] }}" type="checkbox" 
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded disabled:opacity-50"
                                                {{ $facil['status'] !== 'available' ? 'disabled' : '' }}>
                                        </div>

                                        <!-- Avatar / Details -->
                                        <div class="ml-4 flex-1">
                                            <label for="facil_{{ $facil['id'] }}" class="font-medium text-gray-900 block cursor-pointer">
                                                {{ $facil['name'] }}
                                                @if($facil['status'] === 'unqualified')
                                                    <span class="ml-2 text-xs text-red-500 font-normal">({{ $facil['reason'] }})</span>
                                                @elseif($facil['status'] === 'busy')
                                                    <span class="ml-2 text-xs text-yellow-500 font-normal">({{ $facil['reason'] }})</span>
                                                @endif
                                            </label>
                                            <p class="text-sm text-gray-500">{{ $facil['experience'] }} years experience • (Rating: {{ number_format($facil['rating'], 1) }})</p>
                                            <p class="text-xs text-gray-400 mt-1">Skills: {{ Str::limit($facil['skills'], 50) }}</p>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="ml-4">
                                            @if($facil['status'] === 'available')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    available
                                                </span>
                                                <div class="text-xs text-green-600 text-right mt-1 font-bold">Match: {{ $facil['match_score'] }}</div>
                                            @elseif($facil['status'] === 'busy')
                                                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    busy
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                    unqualified
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Sticky Footer for Action -->
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Assign Selected
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right Column: Stats & Recommendations -->
                    <div class="w-80 space-y-6">
                        
                        <!-- Assignment Status -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                Assignment Status
                            </h3>
                            
                            @php
                                $assignedCount = $event->assignments->count();
                                $quota = $event->quota > 0 ? $event->quota : 1; 
                                $percent = min(100, ($assignedCount / $quota) * 100);
                            @endphp

                            <div class="mb-2 flex justify-between text-sm">
                                <span class="text-gray-500">Progress</span>
                                <span class="font-medium text-gray-900">{{ $assignedCount }} / {{ $event->quota }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                            
                            <p class="text-sm text-gray-500 mb-4">
                                {{ max(0, $event->quota - $assignedCount) }} more facilitators needed
                            </p>

                            <div class="flex flex-wrap gap-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                                    {{ $event->event_category ?? 'General' }}
                                </span>
                            </div>
                        </div>

                        <!-- Smart Recommendation Info -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-indigo-100">
                            <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Smart Recommendation
                            </h3>
                            <p class="text-xs text-gray-500 mb-4">
                                Generate recommendations based on facilitator expertise and availability.
                            </p>

                            <div class="bg-blue-50 border border-blue-100 rounded-md p-3 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1 md:flex md:justify-between">
                                        <p class="text-xs text-blue-700">
                                            Our AI-powered system analyzes experience, specialization, and availability to recommend the best matches.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <button onclick="window.location.reload()" class="w-full bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Refresh Recommendations
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
