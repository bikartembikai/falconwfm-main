@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-64 bg-green-600 text-white h-full fixed inset-y-0 left-0 z-30 shadow-xl">
        <div class="p-6 text-2xl font-bold border-b border-green-500 flex items-center gap-2">
            <div class="bg-white text-green-600 p-1 rounded font-mono text-sm">FM</div>
            <span>Facilitator <span class="text-xs block font-normal text-green-100">Portal</span></span>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-green-500 rounded-lg transition text-white">Dashboard</a>
            <a href="{{ route('attendance.clockin_view') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-green-500 rounded-lg transition text-green-50 hover:text-white">Clock In/Out</a>
            <a href="{{ route('assignments.index') }}" class="flex items-center gap-3 px-4 py-3 bg-green-800 rounded-lg shadow-inner text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Assigned Events
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-green-500 rounded-lg transition text-green-50 hover:text-white">Leave Request</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-green-500 rounded-lg transition text-green-50 hover:text-white">Allowance</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 md:ml-64 flex flex-col h-screen overflow-y-auto w-full">
        <!-- Mobile Header -->
        <header class="bg-white shadow-sm sticky top-0 z-20 md:hidden flex justify-between items-center p-4">
            <div class="flex items-center gap-2">
                <div class="bg-green-600 text-white p-1 rounded font-mono text-sm">FM</div>
                <span class="font-bold text-gray-800">Job Board</span>
            </div>
            <button id="mobile-menu-btn" class="text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </header>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-green-600 text-white absolute top-16 left-0 right-0 z-30 shadow-lg border-t border-green-500">
            <a href="{{ route('dashboard') }}" class="block px-4 py-3 border-b border-green-500 hover:bg-green-700">Dashboard</a>
            <a href="{{ route('attendance.clockin_view') }}" class="block px-4 py-3 border-b border-green-500 hover:bg-green-700">Clock In/Out</a>
            <a href="{{ route('assignments.index') }}" class="block px-4 py-3 border-b border-green-500 bg-green-700">Assigned Events</a>
            <a href="#" class="block px-4 py-3 border-b border-green-500 hover:bg-green-700">Leave Request</a>
            <a href="#" class="block px-4 py-3 hover:bg-green-700">Allowance</a>
        </div>

        <main class="p-4 md:p-8 bg-gray-50 flex-1">
            <div class="max-w-6xl mx-auto space-y-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Event Assignments</h1>
                    <p class="text-gray-500 text-sm">View and respond to your event assignments.</p>
                </div>

                <!-- Stats Row (Scrollable on Mobile) -->
                <div class="flex overflow-x-auto gap-4 pb-2 md:grid md:grid-cols-4 md:pb-0">
                    <div class="min-w-[140px] bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-400 font-bold uppercase">Total</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $assignments->count() }}</p>
                    </div>
                    <div class="min-w-[140px] bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-400 font-bold uppercase">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $assignments->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="min-w-[140px] bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-400 font-bold uppercase">Accepted</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $assignments->where('status', 'accepted')->count() }}</p>
                    </div>
                    <div class="min-w-[140px] bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-400 font-bold uppercase">Completed</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $history->count() }}</p>
                    </div>
                </div>

                <!-- Search Filter -->
                <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100 flex gap-2">
                    <input type="text" placeholder="Search events..." class="w-full bg-gray-50 border-none rounded-md text-sm focus:ring-green-500 px-4">
                    <select class="bg-gray-50 border-none rounded-md text-sm focus:ring-green-500 text-gray-600">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>Accepted</option>
                    </select>
                </div>

                <!-- Active Assignments Grid -->
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-gray-800">Upcoming & Pending</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @forelse($assignments as $assignment)
                        <div class="bg-white rounded-xl shadow-sm border {{ $assignment->status == 'accepted' ? 'border-green-200' : 'border-yellow-200' }} overflow-hidden">
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $assignment->event->event_name }}</h3>
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $assignment->status == 'accepted' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </div>
                                <div class="space-y-2 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ \Carbon\Carbon::parse($assignment->event->start_date_time)->format('Y-m-d') }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($assignment->event->start_date_time)->format('H:i') }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $assignment->event->venue ?? 'TBD' }}
                                    </div>
                                </div>
                                <hr class="border-gray-100 mb-4">
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Details</button>
                                    @if($assignment->status == 'pending')
                                    <button class="flex-1 bg-green-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-green-700">Accept</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-2 text-center py-8 text-gray-400 bg-white rounded-xl border border-dashed border-gray-200">
                            No active assignments found.
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Past Events Joined (New Section) -->
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Past Events Joined
                    </h2>
                    
                    <!-- Desktop Table -->
                    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($history as $record)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $record->event->event_name ?? 'Unknown Event' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ \Carbon\Carbon::parse($record->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">Facilitator</td> <!-- Replace with actual role if tracked in history -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Completed
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No past history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Stacked List -->
                    <div class="md:hidden space-y-3">
                        @forelse($history as $record)
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm">{{ $record->event->event_name ?? 'Unknown' }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($record->created_at)->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">Completed</span>
                        </div>
                        @empty
                        <div class="text-center text-gray-400 py-4">No past history.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
@endsection
