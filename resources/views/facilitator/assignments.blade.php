@extends('layouts.dashboard')

@section('content')
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
            @php
                $bgColor = match($assignment->status) {
                    'accepted' => 'bg-green-50 border-green-300',
                    'pending', 'assigned' => 'bg-yellow-50 border-yellow-300',
                    default => 'bg-white border-gray-200'
                };
                $badgeColor = match($assignment->status) {
                    'accepted' => 'bg-green-100 text-green-700',
                    'pending', 'assigned' => 'bg-yellow-100 text-yellow-700',
                    default => 'bg-gray-100 text-gray-700'
                };
            @endphp
            <div class="{{ $bgColor }} rounded-xl shadow-sm border overflow-hidden">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-bold text-gray-800 text-lg">{{ $assignment->event->eventName }}</h3>
                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $badgeColor }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $assignment->event->startDateTime->format('d/m/Y') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold text-gray-800">Clock In: {{ $assignment->event->startDateTime->format('g:i A') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $assignment->event->venue ?? 'TBD' }}
                        </div>
                        <!-- Team Acceptance Status -->
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-200">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-sm">
                                Team: 
                                <span class="font-bold {{ $assignment->event->accepted_count == $assignment->event->total_assigned ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ $assignment->event->accepted_count ?? 0 }}/{{ $assignment->event->total_assigned ?? 0 }} accepted
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 mb-4">
                        <p>Assigned: {{ $assignment->dateAssigned->format('d/m/Y') }}</p>
                        @if($assignment->status == 'accepted')
                        <p>Responded: {{ $assignment->updated_at->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    <hr class="border-gray-200 mb-4">
                    <div class="flex gap-2">
                        <button onclick="openDetailsModal({{ $assignment->assignmentID }})" class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Details
                        </button>
                        @if($assignment->status == 'pending' || $assignment->status == 'assigned')
                        <form action="{{ route('assignments.accept', $assignment->assignmentID) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-green-700 flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Accept
                            </button>
                        </form>
                        <form action="{{ route('assignments.decline', $assignment->assignmentID) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-red-700 flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Reject
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hidden data for modal -->
            <script>
                window.assignmentData = window.assignmentData || {};
                window.assignmentData[{{ $assignment->assignmentID }}] = {
                    eventName: @json($assignment->event->eventName),
                    status: @json(ucfirst($assignment->status)),
                    category: @json($assignment->event->eventCategory ?? 'N/A'),
                    venue: @json($assignment->event->venue ?? 'TBD'),
                    dateAssigned: @json($assignment->dateAssigned->format('d/m/Y')),
                    eventDate: @json($assignment->event->startDateTime->format('d/m/Y')),
                    clockInTime: @json($assignment->event->startDateTime->format('g:i A')),
                    respondedDate: @json($assignment->status == 'accepted' ? $assignment->updated_at->format('d/m/Y') : 'Pending')
                };
            </script>
            @empty
            <div class="col-span-2 text-center py-8 text-gray-400 bg-white rounded-xl border border-dashed border-gray-200">
                No active assignments found.
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-lg font-bold text-gray-800">Assignment Details</h2>
                <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <h3 id="modalEventName" class="text-xl font-bold text-gray-900 mb-2"></h3>
            <span id="modalStatus" class="px-3 py-1 text-sm font-bold rounded-full"></span>
            
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Event Category</p>
                    <p id="modalCategory" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Venue</p>
                    <p id="modalVenue" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Date Assigned</p>
                    <p id="modalDateAssigned" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Event Date</p>
                    <p id="modalEventDate" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Clock In Time</p>
                    <p id="modalClockInTime" class="font-bold text-green-700 text-lg"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Response Date</p>
                    <p id="modalRespondedDate" class="font-medium text-gray-800"></p>
                </div>
            </div>
            
            <div class="mt-6">
                <button onclick="closeDetailsModal()" class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg font-medium hover:bg-gray-200">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function openDetailsModal(assignmentId) {
    const data = window.assignmentData[assignmentId];
    if (!data) return;
    
    document.getElementById('modalEventName').textContent = data.eventName;
    document.getElementById('modalCategory').textContent = data.category;
    document.getElementById('modalVenue').textContent = data.venue;
    document.getElementById('modalDateAssigned').textContent = data.dateAssigned;
    document.getElementById('modalEventDate').textContent = data.eventDate;
    document.getElementById('modalClockInTime').textContent = data.clockInTime;
    document.getElementById('modalRespondedDate').textContent = data.respondedDate;
    
    const statusEl = document.getElementById('modalStatus');
    statusEl.textContent = data.status;
    if (data.status.toLowerCase() === 'accepted') {
        statusEl.className = 'px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-700';
    } else if (data.status.toLowerCase() === 'pending' || data.status.toLowerCase() === 'assigned') {
        statusEl.className = 'px-3 py-1 text-sm font-bold rounded-full bg-yellow-100 text-yellow-700';
    } else {
        statusEl.className = 'px-3 py-1 text-sm font-bold rounded-full bg-gray-100 text-gray-700';
    }
    
    document.getElementById('detailsModal').classList.remove('hidden');
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailsModal();
});
</script>
@endsection
