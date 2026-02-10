@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Attendance Management</h1>
        <p class="text-gray-500 text-sm">Validate and manage facilitator attendance records</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <p class="text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Records</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalRecords }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-green-600 uppercase font-medium">Verified</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $verifiedRecords }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-orange-500 uppercase font-medium">Pending</p>
            <p class="text-3xl font-bold text-orange-500 mt-1">{{ $pendingRecords }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-medium">Absent</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $absentRecords }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-red-600 uppercase font-medium">Rejected</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $rejectedRecords }}</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
        <div class="flex-1 max-w-md">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="searchInput" placeholder="Search by name, event, or assignment ID..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
        <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Records</option>
            <option value="verified">Verified</option>
            <option value="pending">Pending</option>
            <option value="absent">Absent</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facilitator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Assigned</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proof</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="attendanceRows">
                    @forelse($assignments as $assignment)
                    @php
                        $user = $assignment->user;
                        $event = $assignment->event;
                        $initials = $user ? strtoupper(substr($user->name, 0, 1) . substr(strstr($user->name, ' ') ?: '', 1, 1)) : 'NA';
                        $colors = ['from-pink-400 to-rose-500', 'from-blue-400 to-cyan-500', 'from-green-400 to-emerald-500', 'from-purple-400 to-violet-500', 'from-amber-400 to-orange-500'];
                        $colorClass = $colors[$assignment->assignmentID % count($colors)];
                    @endphp
                    <tr class="attendance-row" data-name="{{ strtolower($user->name ?? '') }}" data-event="{{ strtolower($event->eventName ?? '') }}" data-status="{{ $assignment->attendanceStatus ?? 'pending' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $colorClass }} flex items-center justify-center text-white font-bold text-sm">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $assignment->assignmentID }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $event->eventName ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Event #{{ $event->eventID ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($assignment->dateAssigned)
                                <div>{{ $assignment->dateAssigned->format('m/d/Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $assignment->dateAssigned->format('h:i A') }}</div>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $assignment->clockInTime ? $assignment->clockInTime->format('H:i:s') : 'NULL' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $assignment->clockOutTime ? $assignment->clockOutTime->format('H:i:s') : 'NULL' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColor = match($assignment->status) {
                                    'completed' => 'bg-green-100 text-green-700',
                                    'accepted' => 'bg-blue-100 text-blue-700',
                                    'assigned', 'pending' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                {{ ucfirst($assignment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $attStatus = $assignment->attendanceStatus ?? 'pending';
                                $attColor = match($attStatus) {
                                    'verified' => 'text-green-600',
                                    'present', 'pending' => 'text-orange-500',
                                    'absent' => 'text-gray-600',
                                    'rejected' => 'text-red-600',
                                    default => 'text-gray-500'
                                };
                            @endphp
                            <span class="flex items-center gap-1 {{ $attColor }} font-medium text-sm">
                                ⊙ {{ ucfirst($attStatus) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($assignment->imageProof)
                            <button onclick="openProofModal({{ $assignment->assignmentID }}, '{{ $user->name ?? 'Unknown' }}', '{{ $event->eventName ?? 'N/A' }}', '{{ $assignment->clockInTime ? $assignment->clockInTime->format('H:i:s') : 'N/A' }}', '{{ $assignment->clockOutTime ? $assignment->clockOutTime->format('H:i:s') : 'N/A' }}', '{{ asset('storage/' . $assignment->imageProof) }}')" class="text-blue-600 hover:text-blue-800 flex items-center gap-1 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                View
                            </button>
                            @else
                            <span class="text-gray-400 text-sm">No proof</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select onchange="updateAttendance({{ $assignment->assignmentID }}, this.value)" class="px-2 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                                <option value="pending" {{ ($assignment->attendanceStatus ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ $assignment->attendanceStatus == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="absent" {{ $assignment->attendanceStatus == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="rejected" {{ $assignment->attendanceStatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">No attendance records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-sm text-gray-500">
            Showing {{ $assignments->count() }} of {{ $assignments->count() }} attendance records
        </div>
    </div>
</div>

<!-- Proof Modal -->
<div id="proofModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Attendance Proof</h2>
                    <p id="modalSubtitle" class="text-gray-500 text-sm"></p>
                </div>
                <button onclick="closeProofModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Info Row -->
            <div class="grid grid-cols-2 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-xs text-gray-500">Assignment ID</p>
                    <p id="modalAssignmentId" class="font-bold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Facilitator</p>
                    <p id="modalFacilitator" class="font-bold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Clock In</p>
                    <p id="modalClockIn" class="font-bold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Clock Out</p>
                    <p id="modalClockOut" class="font-bold text-gray-800"></p>
                </div>
            </div>

            <!-- Image Proof -->
            <div class="bg-gray-100 rounded-lg p-8 flex flex-col items-center justify-center mb-4">
                <img id="modalProofImage" src="" alt="Attendance Proof" class="max-w-full max-h-64 rounded-lg shadow-md hidden">
                <div id="noProofPlaceholder" class="text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-gray-500 font-medium">Image Proof</p>
                    <p id="modalProofFilename" class="text-gray-400 text-sm"></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <form id="verifyForm" method="POST" class="flex-1">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="attendanceStatus" value="verified">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Verify Attendance
                    </button>
                </form>
                <form id="rejectForm" method="POST" class="flex-1">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="attendanceStatus" value="rejected">
                    <button type="submit" class="w-full bg-white border border-red-300 text-red-600 font-medium py-2 rounded-lg hover:bg-red-50 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Reject
                    </button>
                </form>
            </div>

            <div class="mt-4 text-center">
                <button onclick="closeProofModal()" class="text-gray-500 hover:text-gray-700 font-medium">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function openProofModal(assignmentId, facilitator, eventName, clockIn, clockOut, proofUrl) {
    document.getElementById('modalSubtitle').textContent = facilitator + ' - ' + eventName;
    document.getElementById('modalAssignmentId').textContent = assignmentId;
    document.getElementById('modalFacilitator').textContent = facilitator;
    document.getElementById('modalClockIn').textContent = clockIn;
    document.getElementById('modalClockOut').textContent = clockOut;
    
    const img = document.getElementById('modalProofImage');
    const placeholder = document.getElementById('noProofPlaceholder');
    
    if (proofUrl && proofUrl !== '') {
        img.src = proofUrl;
        img.classList.remove('hidden');
        placeholder.classList.add('hidden');
        document.getElementById('modalProofFilename').textContent = proofUrl.split('/').pop();
    } else {
        img.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }
    
    document.getElementById('verifyForm').action = '/admin/attendance/' + assignmentId;
    document.getElementById('rejectForm').action = '/admin/attendance/' + assignmentId;
    document.getElementById('proofModal').classList.remove('hidden');
}

function closeProofModal() {
    document.getElementById('proofModal').classList.add('hidden');
}

function updateAttendance(assignmentId, status) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/attendance/' + assignmentId;
    form.innerHTML = `
        @csrf
        @method('PUT')
        <input type="hidden" name="attendanceStatus" value="${status}">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    
    document.querySelectorAll('.attendance-row').forEach(row => {
        const name = row.dataset.name;
        const event = row.dataset.event;
        const rowStatus = row.dataset.status;
        
        const matchesQuery = name.includes(query) || event.includes(query);
        const matchesStatus = !status || rowStatus === status;
        
        row.style.display = (matchesQuery && matchesStatus) ? '' : 'none';
    });
}

document.getElementById('proofModal').addEventListener('click', function(e) {
    if (e.target === this) closeProofModal();
});
</script>
@endsection
