@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
        <p class="mt-1 text-gray-500">Here's what's happening with your operations today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Facilitators -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Facilitators</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalFacilitators }}</p>
                <div class="mt-2 text-xs text-gray-500">+3 this month</div>
            </div>
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Active Events -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500">Active Events</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $activeEvents }}</p>
                <div class="mt-2 text-xs text-gray-500">8 this week</div>
            </div>
            <div class="p-2 bg-green-50 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <!-- Pending Leave Requests -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Leave Requests</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingLeaves }}</p>
                <div class="mt-2 text-xs text-gray-500">Requires attention</div>
            </div>
            <div class="p-2 bg-orange-50 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Payments</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingPayments }}</p>
                <div class="mt-2 text-xs text-gray-500">Requires attention</div>
            </div>
            <div class="p-2 bg-red-50 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activities -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Recent Activities</h3>
            <div class="space-y-6">
                @foreach($recentActivities as $activity)
                <div class="pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                    <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Upcoming Events</h3>
            <div class="space-y-6">
                @forelse($upcomingEvents as $event)
                <div class="flex justify-between items-center pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $event->eventName }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $event->startDateTime->format('M d, Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="text-sm text-gray-500">No upcoming events found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
