@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar via Component or Copy/Paste for now to ensure isolation -->
    @include('components.facilitator_sidebar') <!-- Assuming we extract sidebar later, or duplicate for now -->

    <!-- Main Content -->
    <div class="flex-1 md:ml-64 flex flex-col h-screen overflow-y-auto w-full">
        <!-- Mobile Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20 md:hidden flex justify-between items-center p-4">
            <div class="flex items-center gap-2">
                <div class="bg-green-600 text-white p-1 rounded font-mono text-sm">FM</div>
                <span class="font-bold text-gray-800">Clock In/Out</span>
            </div>
            <button id="mobile-menu-btn" class="text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </header>

        <!-- Reuse Mobile Menu from Dashboard (Ideally this is a layout component) -->
        <div id="mobile-menu" class="hidden md:hidden bg-green-600 text-white absolute top-16 left-0 right-0 z-30 shadow-lg border-t border-green-500">
            <a href="{{ route('facilitator.dashboard') }}" class="block px-4 py-3 border-b border-green-500 hover:bg-green-700">Dashboard</a>
            <a href="{{ route('attendance.clockin_view') }}" class="block px-4 py-3 border-b border-green-500 bg-green-700">Clock In/Out</a>
            <a href="{{ route('assignments.index') }}" class="block px-4 py-3 border-b border-green-500 hover:bg-green-700">Assigned Events</a>
            <a href="#" class="block px-4 py-3 border-b border-green-500 hover:bg-green-700">Leave Request</a>
            <a href="#" class="block px-4 py-3 hover:bg-green-700">Allowance Request</a>
        </div>

        <main class="p-4 md:p-8 bg-gray-50 flex-1">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Record Attendance</h1>

                <!-- Active Event Card -->
                @if($activeEvent)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Active Event</h2>
                        
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-green-700">{{ $activeEvent->event_name }}</h3>
                                <div class="mt-2 space-y-1 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>{{ \Carbon\Carbon::parse($activeEvent->start_date_time)->format('D, d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>{{ \Carbon\Carbon::parse($activeEvent->start_date_time)->format('H:i') }} - {{ $activeEvent->end_date_time ? \Carbon\Carbon::parse($activeEvent->end_date_time)->format('H:i') : 'TBD' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>{{ $activeEvent->venue }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Area -->
                            <div class="w-full md:w-auto bg-gray-50 rounded-lg p-4 flex flex-col items-center justify-center min-w-[250px]">
                                @if($currentAttendance && $currentAttendance->clock_in_time && !$currentAttendance->clock_out_time)
                                    <!-- Clocked In State -->
                                    <div class="text-center w-full">
                                        <div class="mb-3 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium inline-flex items-center gap-2">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                            Clocked In at {{ \Carbon\Carbon::parse($currentAttendance->clock_in_time)->format('H:i') }}
                                        </div>
                                        <button onclick="openModal('out')" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg shadow transition transform active:scale-95 flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Clock Out
                                        </button>
                                    </div>
                                @elseif($currentAttendance && $currentAttendance->clock_out_time)
                                    <div class="text-center w-full">
                                        <div class="mb-3 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                            Completed
                                        </div>
                                        <p class="text-sm text-gray-500">Shift ended at {{ \Carbon\Carbon::parse($currentAttendance->clock_out_time)->format('H:i') }}</p>
                                    </div>
                                @else
                                    <!-- Ready to Clock In -->
                                    <div class="text-center w-full">
                                        <p class="text-sm text-gray-500 mb-3">Not clocked in today</p>
                                        <button onclick="openModal('in')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:shadow-green-500/30 transition transform active:scale-95 flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Clock In
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Based on your schedule, you have no events assigned for today. (<a href="{{ route('assignments.index') }}" class="font-medium underline hover:text-yellow-600">Check Assignments</a>)
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- History Section -->
                <h2 class="text-lg font-bold text-gray-800 mb-4">Attendance History</h2>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">In</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Out</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($history as $record)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $record->event->event_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($record->created_at)->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $record->clock_in_time ? \Carbon\Carbon::parse($record->clock_in_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $record->clock_out_time ? \Carbon\Carbon::parse($record->clock_out_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-bold">
                                        {{ $record->hours_worked ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $record->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">No attendance records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modals -->
<!-- Clock In Modal -->
<div id="modal-in" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('in')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Confirm Clock In</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                You are about to clock in for <span class="font-bold text-gray-800">{{ $activeEvent ? $activeEvent->event_name : '' }}</span>.
                                <br>Current Time: <span class="font-mono text-gray-700">{{ now()->format('H:i') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form action="{{ route('attendance.clockIn') }}" method="POST">
                    @csrf
                    @if($activeEvent)
                    <input type="hidden" name="event_id" value="{{ $activeEvent->id }}">
                    @endif
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Clock In
                    </button>
                </form>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('in')">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clock Out Modal -->
<div id="modal-out" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('out')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Confirm Clock Out</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to clock out now?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                @if($currentAttendance)
                <form action="{{ route('attendance.clockOut', $currentAttendance->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Clock Out
                    </button>
                </form>
                @endif
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('out')">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');

    if(btn){
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    }

    function openModal(type) {
        document.getElementById('modal-' + type).classList.remove('hidden');
    }

    function closeModal(type) {
        document.getElementById('modal-' + type).classList.add('hidden');
    }
</script>
@endsection
