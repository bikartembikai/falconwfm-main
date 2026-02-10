@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Leave Request List</h1>
        <p class="text-gray-500 text-sm">Manage and assign facilitators to upcoming events</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <p class="text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Requests</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalLeaves }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-medium">Approved</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $approvedLeaves }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-medium">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pendingLeaves }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-medium">Rejected</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $rejectedLeaves }}</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
        <div class="flex-1 max-w-md">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="searchInput" placeholder="Search by name, email, or account number..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
        <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500">
            <option value="">All Requests</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facilitator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="leaveRows">
                    @forelse($leaves as $leave)
                    @php
                        $user = $leave->user;
                        $initials = $user ? strtoupper(substr($user->name, 0, 1) . substr(strstr($user->name, ' ') ?: '', 1, 1)) : 'NA';
                        $colors = ['from-pink-400 to-rose-500', 'from-blue-400 to-cyan-500', 'from-green-400 to-emerald-500', 'from-purple-400 to-violet-500', 'from-amber-400 to-orange-500'];
                        $colorClass = $colors[$leave->leaveID % count($colors)];
                    @endphp
                    <tr class="leave-row" data-name="{{ strtolower($user->name ?? '') }}" data-status="{{ $leave->status }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $colorClass }} flex items-center justify-center text-white font-bold text-sm">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">Leave {{ $leave->leaveID }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $leave->startDate ? \Carbon\Carbon::parse($leave->startDate)->format('m/d/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($leave->status) {
                                    'approved' => 'text-green-600',
                                    'pending' => 'text-orange-500',
                                    'rejected' => 'text-red-600',
                                    default => 'text-gray-600'
                                };
                                $statusIcon = match($leave->status) {
                                    'approved' => '⊙',
                                    'pending' => '⊙',
                                    'rejected' => '⊙',
                                    default => '•'
                                };
                            @endphp
                            <span class="flex items-center gap-1 {{ $statusClass }} font-medium text-sm">
                                {{ $statusIcon }} {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $leave->reason ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <button onclick="openUpdateModal({{ $leave->leaveID }}, '{{ $leave->status }}')" class="px-3 py-1 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                                    Update
                                </button>
                                @if($leave->status === 'pending' || $leave->status === 'rejected')
                                <form action="{{ route('leaves.destroy', $leave->leaveID) }}" method="POST" onsubmit="return confirm('Delete this leave request?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 border border-red-300 text-red-600 rounded-lg text-sm hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">No leave requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-sm text-gray-500">
            Showing {{ $leaves->count() }} of {{ $leaves->count() }} leave requests
        </div>
    </div>
</div>

<!-- Update Leave Modal -->
<div id="updateModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-lg font-bold text-gray-800">Update Leave Status</h2>
                <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="updateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Leave Status</label>
                        <select name="status" id="modalStatus" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeUpdateModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUpdateModal(leaveId, currentStatus) {
    document.getElementById('updateForm').action = '/leaves/' + leaveId;
    document.getElementById('modalStatus').value = currentStatus;
    document.getElementById('updateModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
}

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    
    document.querySelectorAll('.leave-row').forEach(row => {
        const name = row.dataset.name;
        const rowStatus = row.dataset.status;
        
        const matchesQuery = name.includes(query);
        const matchesStatus = !status || rowStatus === status;
        
        row.style.display = (matchesQuery && matchesStatus) ? '' : 'none';
    });
}

document.getElementById('updateModal').addEventListener('click', function(e) {
    if (e.target === this) closeUpdateModal();
});
</script>
@endsection
