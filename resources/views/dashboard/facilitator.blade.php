@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Welcome, {{ $user->name }}!</h1>
        <p class="text-slate-500 mt-1">Here's your facilitator portal dashboard</p>
    </div>

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Assignments -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-slate-500 mb-2">Total Assignments</h3>
                <div class="text-4xl font-bold text-slate-900">{{ $totalAssignments }}</div>
            </div>
            <div class="absolute right-4 top-4 p-3 bg-slate-50 rounded-lg text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                 <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Pending Response -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-slate-500 mb-2">Pending Response</h3>
                <div class="text-4xl font-bold {{ $pendingResponse > 0 ? 'text-orange-600' : 'text-slate-900' }}">{{ $pendingResponse }}</div>
            </div>
            <div class="absolute right-4 top-4 p-3 bg-slate-50 rounded-lg text-slate-400 group-hover:bg-orange-50 group-hover:text-orange-600 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Pending Allowance -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-slate-500 mb-2">Pending Allowance</h3>
                <div class="text-4xl font-bold {{ $pendingAllowance > 0 ? 'text-purple-600' : 'text-slate-900' }}">{{ $pendingAllowance }}</div>
            </div>
            <div class="absolute right-4 top-4 p-3 bg-slate-50 rounded-lg text-slate-400 group-hover:bg-purple-50 group-hover:text-purple-600 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div>
        <h2 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Clock In/Out -->
            <a href="{{ route('attendance.clockin_view') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:border-blue-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="h-14 w-14 bg-blue-500 rounded-xl flex items-center justify-center text-white mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-blue-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 group-hover:text-blue-600">Clock In/Out</h3>
                <p class="text-xs text-slate-500 mt-1">Record your attendance</p>
            </a>

            <!-- View Assignments -->
            <a href="{{ route('assignments.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:border-purple-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="h-14 w-14 bg-purple-500 rounded-xl flex items-center justify-center text-white mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-purple-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 group-hover:text-purple-600">View Assignments</h3>
                <p class="text-xs text-slate-500 mt-1">View event assignments</p>
            </a>

            <!-- Request Leave -->
            <a href="#" class="group bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:border-orange-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="h-14 w-14 bg-orange-500 rounded-xl flex items-center justify-center text-white mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-orange-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 group-hover:text-orange-600">Request Leave</h3>
                <p class="text-xs text-slate-500 mt-1">Apply for leave</p>
            </a>

            <!-- Request Allowance -->
            <a href="#" class="group bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:border-green-500 hover:shadow-md transition-all text-center flex flex-col items-center">
                <div class="h-14 w-14 bg-green-500 rounded-xl flex items-center justify-center text-white mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-green-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 group-hover:text-green-600">Request Allowance</h3>
                <p class="text-xs text-slate-500 mt-1">Request expense allowance</p>
            </a>
        </div>
    </div>

    <!-- Bottom Lists Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Assignments -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 flex items-center">
                    <svg class="w-5 h-5 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Recent Assignments
                </h3>
            </div>
            <div class="p-6 flex-1">
                @if($recentAssignments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentAssignments as $assignment)
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900">{{ $assignment->event->eventName }}</h4>
                                    <p class="text-xs text-slate-500">{{ $assignment->event->startDateTime->format('M d, Y') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->status == 'accepted' ? 'bg-green-100 text-green-800' : ($assignment->status == 'pending' ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-800') }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex items-center justify-center text-slate-400 text-sm py-8">
                        No assignments yet
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Allowance Requests -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 flex items-center">
                    <svg class="w-5 h-5 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Recent Allowance Requests
                </h3>
            </div>
            <div class="p-6 flex-1">
                @if($allowanceRequests->count() > 0)
                    <div class="space-y-4">
                        @foreach($allowanceRequests as $payment)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900">{{ $payment->assignment->event->eventName ?? 'General Expense' }}</h4>
                                    <p class="text-xs text-slate-500">₱ {{ number_format($payment->amount, 2) }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->paymentStatus == 'processed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($payment->paymentStatus) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex items-center justify-center text-slate-400 text-sm py-8">
                        No recent requests
                    </div>
                @endif
            </div>
            @if($allowanceRequests->count() > 0)
            <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 rounded-b-xl">
                 <button class="w-full text-center text-sm font-medium text-white bg-[#1a8a5f] hover:bg-[#15704d] py-2 rounded-lg transition-colors">
                    View All Allowances
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
