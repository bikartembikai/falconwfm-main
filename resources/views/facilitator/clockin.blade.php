@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Clock In / Clock Out</h1>
        <p class="text-gray-500 text-sm">Record your attendance for assigned events</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <p class="text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
        <p class="text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Active Events Section -->
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4">Active Events</h2>
        
        @if($activeAssignment)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">{{ $activeAssignment->event->eventName }}</h3>
            
            <div class="space-y-2 text-sm text-gray-600 mb-6">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Date: {{ $activeAssignment->event->startDateTime->format('d/m/Y') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Time: {{ $activeAssignment->event->startDateTime->format('H:i') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Venue: {{ $activeAssignment->event->venue ?? 'TBD' }}</span>
                </div>
            </div>

            @if($activeAssignment->clockInTime && !$activeAssignment->clockOutTime)
                <!-- Clocked In - Show status and Clock Out option -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-2 text-blue-700">
                        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                        <span class="font-medium">Clocked in at {{ \Carbon\Carbon::parse($activeAssignment->clockInTime)->format('H:i') }}</span>
                    </div>
                </div>
                
                @if(!$activeAssignment->imageProof)
                <!-- Image Proof Upload Section -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <p class="text-yellow-700 text-sm mb-3 font-medium">Please upload proof of attendance</p>
                    <form action="{{ route('attendance.uploadProof', $activeAssignment->assignmentID) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-2">
                            <label class="flex-1 cursor-pointer">
                                <div class="flex items-center justify-center gap-2 bg-white border-2 border-dashed border-yellow-300 rounded-lg p-4 hover:bg-yellow-50 transition">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-yellow-700 font-medium" id="proofFileName">Take Photo or Choose File</span>
                                </div>
                                <input type="file" name="image_proof" accept="image/*" capture="environment" class="hidden" onchange="updateFileName(this)" required>
                            </label>
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-6 rounded-lg transition">
                                Upload Proof
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-2 text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-medium">Proof uploaded successfully</span>
                    </div>
                </div>
                @endif

                @php
                    $canClockOut = !$activeAssignment->event->endDateTime || now()->gte($activeAssignment->event->endDateTime);
                @endphp

                @if($canClockOut)
                <button onclick="openModal('out')" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg shadow transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Clock Out
                </button>
                @else
                <div class="bg-gray-100 border border-gray-200 rounded-lg p-4 mb-4 text-center">
                    <p class="text-gray-600 text-sm">Clock out available after event ends</p>
                    <p class="text-gray-800 font-bold">{{ $activeAssignment->event->endDateTime->format('g:i A') }}</p>
                </div>
                <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3 px-4 rounded-lg cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Clock Out (Locked)
                </button>
                @endif
            @elseif($activeAssignment->clockOutTime)
                <!-- Completed -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-medium">Shift completed at {{ \Carbon\Carbon::parse($activeAssignment->clockOutTime)->format('H:i') }}</span>
                    </div>
                </div>
            @else
                <!-- Ready to Clock In -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <p class="text-orange-600 font-medium">Not clocked in today</p>
                </div>
                <button onclick="openModal('in')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Clock In
                </button>
            @endif
        </div>
        @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <p class="text-gray-500">No events assigned for today</p>
            <a href="{{ route('assignments.index') }}" class="text-green-600 hover:underline text-sm mt-2 inline-block">View Assignments</a>
        </div>
        @endif
    </div>

    <!-- Attendance History -->
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4">Attendance History</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($history as $record)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $record->event->eventName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->event->startDateTime->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->clockInTime ? \Carbon\Carbon::parse($record->clockInTime)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->clockOutTime ? \Carbon\Carbon::parse($record->clockOutTime)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $record->hours_worked ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColor = match($record->attendanceStatus) {
                                        'verified' => 'bg-green-100 text-green-800 border-green-200',
                                        'pending' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'absent' => 'bg-red-100 text-red-800 border-red-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full border {{ $statusColor }}">
                                    {{ ucfirst($record->attendanceStatus) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No attendance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Clock In Modal -->
<div id="modal-in" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Confirm Clock In</h3>
            </div>
            <p class="text-gray-600 mb-6">
                You are about to clock in for <strong>{{ $activeAssignment ? $activeAssignment->event->eventName : '' }}</strong>.
                <br>Current Time: <span class="font-mono">{{ now()->format('H:i') }}</span>
            </p>
            <div class="flex gap-3">
                <button onclick="closeModal('in')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-lg">Cancel</button>
                <form action="{{ route('attendance.clockIn') }}" method="POST" class="flex-1">
                    @csrf
                    @if($activeAssignment)
                    <input type="hidden" name="event_id" value="{{ $activeAssignment->event->eventID }}">
                    @endif
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-lg">Clock In</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Clock Out Modal -->
<div id="modal-out" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Confirm Clock Out</h3>
            </div>
            <p class="text-gray-600 mb-6">Are you sure you want to clock out now?</p>
            <div class="flex gap-3">
                <button onclick="closeModal('out')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-lg">Cancel</button>
                @if($activeAssignment)
                <form action="{{ route('attendance.clockOut', $activeAssignment->assignmentID) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg">Clock Out</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function openModal(type) {
    document.getElementById('modal-' + type).classList.remove('hidden');
}

function closeModal(type) {
    document.getElementById('modal-' + type).classList.add('hidden');
}

function updateFileName(input) {
    const fileName = input.files[0]?.name || 'Take Photo or Choose File';
    document.getElementById('proofFileName').textContent = fileName;
}

// Close modals when clicking outside
document.querySelectorAll('[id^="modal-"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endsection
