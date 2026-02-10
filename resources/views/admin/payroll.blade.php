@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Payroll Management</h1>
        <p class="text-gray-500 text-sm">Process salary and allowance payments for facilitators</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <p class="text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 font-medium">Total Payments</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPayments }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-orange-500 font-medium">Pending</p>
            <p class="text-3xl font-bold text-orange-500 mt-1">{{ $pendingPayments }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-green-600 font-medium">Approved</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $approvedPayments }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-blue-600 font-medium">Paid</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $paidPayments }}</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex-1 max-w-md">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="searchInput" placeholder="Search by name, event, or payment ID..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
        <div class="flex gap-3">
            <select id="typeFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white min-w-[140px]">
                <option value="">All Types</option>
                <option value="salary">Salary</option>
                <option value="allowance">Allowance</option>
            </select>
            <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white min-w-[140px]">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="paid">Paid</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facilitator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proof</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="paymentRows">
                    @forelse($payments as $payment)
                    @php
                        $user = $payment->assignment->user ?? null;
                        $event = $payment->assignment->event ?? null;
                    @endphp
                    <tr class="payment-row" data-name="{{ strtolower($user->name ?? '') }}" data-event="{{ strtolower($event->eventName ?? '') }}" data-status="{{ $payment->paymentStatus }}" data-type="{{ $payment->paymentType }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $payment->assignment->assignmentID ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $event->eventName ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Assignment #{{ $payment->assignment->assignmentID ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $payment->title ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ number_format($payment->amount, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeColor = match($payment->paymentType) {
                                    'salary' => 'bg-blue-100 text-blue-700',
                                    'allowance' => 'bg-purple-100 text-purple-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $typeColor }}">
                                {{ ucfirst($payment->paymentType ?? 'Salary') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColor = match($payment->paymentStatus) {
                                    'paid' => 'text-green-600 bg-green-50 border-green-200',
                                    'approved' => 'text-green-600 bg-green-50 border-green-200',
                                    'pending' => 'text-orange-500 bg-orange-50 border-orange-200',
                                    'rejected' => 'text-red-600 bg-red-50 border-red-200',
                                    default => 'text-gray-600 bg-gray-50 border-gray-200'
                                };
                                $statusIcon = match($payment->paymentStatus) {
                                    'paid', 'approved' => '⊙',
                                    'pending' => '⊙',
                                    'rejected' => '⊙',
                                    default => '⊙'
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-sm font-medium rounded-full border {{ $statusColor }}">
                                {{ $statusIcon }} {{ ucfirst($payment->paymentStatus) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->paymentDate ? $payment->paymentDate->format('m/d/Y') : 'Not paid' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->paymentProof)
                            <button onclick="openPaymentProofModal('{{ $user->name ?? 'Unknown' }}', '{{ $event->eventName ?? 'N/A' }}', '{{ number_format($payment->amount, 0) }}', '{{ $payment->paymentDate ? $payment->paymentDate->format('m/d/Y') : 'N/A' }}', '{{ asset('storage/' . $payment->paymentProof) }}', '{{ pathinfo($payment->paymentProof, PATHINFO_EXTENSION) }}')" class="text-blue-600 hover:text-blue-800 flex items-center gap-1 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                View
                            </button>
                            @else
                            <span class="text-gray-400 text-sm">No proof</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->paymentStatus === 'paid')
                                <span class="text-gray-500 text-sm font-medium">Completed</span>
                            @elseif($payment->paymentStatus === 'approved')
                                <form action="{{ route('payments.update', $payment->paymentID) }}" method="POST" enctype="multipart/form-data" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" name="payment_proof" id="proof_{{ $payment->paymentID }}" class="hidden" accept="image/*,application/pdf" onchange="this.form.submit()">
                                    <button type="button" onclick="document.getElementById('proof_{{ $payment->paymentID }}').click()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pay
                                    </button>
                                </form>
                            @elseif($payment->paymentStatus === 'pending')
                                <form action="{{ route('payments.update', $payment->paymentID) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="approve" value="1">
                                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium">
                                        Approve
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">No payment records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
<div id="paymentProofModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Payment Proof</h2>
                    <p id="paymentModalSubtitle" class="text-gray-500 text-sm"></p>
                </div>
                <button onclick="closePaymentProofModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Info Row -->
            <div class="grid grid-cols-2 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-xs text-gray-500">Facilitator</p>
                    <p id="paymentModalFacilitator" class="font-bold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Event</p>
                    <p id="paymentModalEvent" class="font-bold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Amount</p>
                    <p id="paymentModalAmount" class="font-bold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Payment Date</p>
                    <p id="paymentModalDate" class="font-bold text-gray-800"></p>
                </div>
            </div>

            <!-- Proof Content -->
            <div id="proofContentContainer" class="bg-gray-100 rounded-lg p-4 flex flex-col items-center justify-center mb-4 overflow-auto max-h-[50vh]">
                <img id="paymentProofImage" src="" alt="Payment Proof" class="max-w-full max-h-96 rounded-lg shadow-md hidden">
                <iframe id="paymentProofPdf" src="" class="w-full h-96 rounded-lg hidden"></iframe>
                <div id="proofPlaceholder" class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-gray-500 font-medium">Loading Proof...</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <a id="downloadProofLink" href="" download class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download
                </a>
                <button onclick="closePaymentProofModal()" class="flex-1 bg-white border border-gray-300 text-gray-700 font-medium py-2 rounded-lg hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('typeFilter').addEventListener('change', filterTable);

function filterTable() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    
    document.querySelectorAll('.payment-row').forEach(row => {
        const name = row.dataset.name;
        const event = row.dataset.event;
        const rowStatus = row.dataset.status;
        const rowType = row.dataset.type;
        
        const matchesQuery = name.includes(query) || event.includes(query);
        const matchesStatus = !status || rowStatus === status;
        const matchesType = !type || rowType === type;
        
        row.style.display = (matchesQuery && matchesStatus && matchesType) ? '' : 'none';
    });
}

function openPaymentProofModal(facilitator, eventName, amount, paymentDate, proofUrl, fileExt) {
    document.getElementById('paymentModalSubtitle').textContent = facilitator + ' - ' + eventName;
    document.getElementById('paymentModalFacilitator').textContent = facilitator;
    document.getElementById('paymentModalEvent').textContent = eventName;
    document.getElementById('paymentModalAmount').textContent = '₱ ' + amount;
    document.getElementById('paymentModalDate').textContent = paymentDate;
    document.getElementById('downloadProofLink').href = proofUrl;
    
    const img = document.getElementById('paymentProofImage');
    const pdf = document.getElementById('paymentProofPdf');
    const placeholder = document.getElementById('proofPlaceholder');
    
    // Hide all first
    img.classList.add('hidden');
    pdf.classList.add('hidden');
    placeholder.classList.add('hidden');
    
    if (proofUrl) {
        const ext = fileExt.toLowerCase();
        if (ext === 'pdf') {
            pdf.src = proofUrl;
            pdf.classList.remove('hidden');
        } else {
            img.src = proofUrl;
            img.classList.remove('hidden');
        }
    } else {
        placeholder.classList.remove('hidden');
    }
    
    document.getElementById('paymentProofModal').classList.remove('hidden');
}

function closePaymentProofModal() {
    document.getElementById('paymentProofModal').classList.add('hidden');
    document.getElementById('paymentProofImage').src = '';
    document.getElementById('paymentProofPdf').src = '';
}

// Close modal on backdrop click
document.getElementById('paymentProofModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentProofModal();
});
</script>
@endsection
