@extends('layouts.dashboard')

@section('content')
<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake {
        animation: shake 0.3s ease-in-out;
        border-color: #ef4444 !important; /* Red border */
    }
</style>

<div class="max-w-6xl mx-auto space-y-8" x-data="{ 
    showModal: false, 
    shakeEvent: false,
    validateAndSubmit() {
        const eventInput = this.$refs.eventInput;
        if (!eventInput.value) {
            this.shakeEvent = true;
            setTimeout(() => this.shakeEvent = false, 300);
            return;
        }
        this.$refs.paymentForm.submit();
    }
}">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Payment Request</h1>
            <p class="text-gray-500 text-sm">Submit requests for travel, meals, accommodation, and other expenses</p>
        </div>
        <button @click="showModal = true" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Request
        </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase">Total</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $payments->count() }}</p>
        </div>
        <!-- Pending -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase text-orange-500">Pending</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $payments->where('paymentStatus', 'pending')->count() }}</p>
        </div>
        <!-- Paid -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase text-blue-500">Paid</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $payments->where('paymentStatus', 'paid')->count() }}</p>
        </div>
        <!-- Total Amount -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase">Total Amount</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">RM {{ number_format($payments->where('paymentStatus', 'paid')->sum('amount')) }}</p>
        </div>
    </div>

    <!-- Main Content: List -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-gray-800">Your Payment Requests</h2>
        
        <div class="space-y-4">
            @forelse($payments as $payment)
            <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-start gap-4 flex-1">
                    <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 text-lg">{{ $payment->title ?? 'Payment' }}</h3>
                        <p class="text-sm text-gray-500">Event: {{ $payment->assignment->event->eventName ?? 'Unknown Event' }}</p>
                        
                        <div class="mt-3 space-y-1">
                            <p class="text-sm font-medium text-gray-800">Amount: RM {{ number_format($payment->amount) }}</p>
                            <p class="text-xs text-gray-500">Requested: {{ $payment->paymentDate ? $payment->paymentDate->format('m/d/Y') : $payment->created_at->format('m/d/Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col items-end gap-2">
                    @php
                        $statusColor = match($payment->paymentStatus) {
                            'paid' => 'bg-green-100 text-green-700 border-green-200',
                            'approved' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'pending' => 'bg-orange-100 text-orange-700 border-orange-200',
                            'rejected' => 'bg-red-100 text-red-700 border-red-200',
                            default => 'bg-gray-100 text-gray-700 border-gray-200'
                        };
                    @endphp
                    <span class="px-3 py-1 {{ $statusColor }} text-xs font-bold rounded-full border uppercase tracking-wide">
                        {{ ucfirst($payment->paymentStatus) }}
                    </span>
                    @if($payment->paymentStatus === 'paid' && $payment->paymentProof)
                        <button onclick="openProofModal('{{ $payment->title }}', '{{ $payment->assignment->event->eventName ?? 'Event' }}', '{{ number_format($payment->amount) }}', '{{ asset('storage/' . $payment->paymentProof) }}', '{{ pathinfo($payment->paymentProof, PATHINFO_EXTENSION) }}')" class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            View Proof
                        </button>
                    @endif
                </div>
            </div>

            @empty
            <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-200">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <p class="text-gray-500">No payment requests found.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg relative z-10 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">New Payment Request</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form x-ref="paymentForm" action="{{ route('facilitator.payments.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event *</label>
                        <select x-ref="eventInput" :class="{'animate-shake': shakeEvent}" name="assignment_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm transition-all duration-200">
                            <option value="">Select Event</option>
                            @foreach($assignments as $assign)
                                <option value="{{ $assign->id ?? $assign->assignmentID }}">{{ $assign->event->eventName ?? 'Event' }} ({{ \Carbon\Carbon::parse($assign->event->startDateTime)->format('M d') }})</option>
                            @endforeach
                        </select>
                        @error('assignment_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Type *</label>
                        <select name="paymentType" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                            <option value="salary">Salary</option>
                            <option value="allowance">Allowance</option>
                        </select>
                        @error('paymentType')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" name="title" placeholder="e.g. Travel Expenses" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" name="amount" placeholder="0.00" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                    </div>


                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                    <button type="button" @click="validateAndSubmit()" class="px-4 py-2 text-sm font-medium text-white bg-green-700 hover:bg-green-800 rounded-lg shadow-sm transition-colors">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
<div id="proofModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Payment Proof</h2>
                    <p id="proofSubtitle" class="text-gray-500 text-sm"></p>
                </div>
                <button onclick="closeProofModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Info Row -->
            <div class="grid grid-cols-2 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-xs text-gray-500">Payment Title</p>
                    <p id="proofTitle" class="font-bold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Event</p>
                    <p id="proofEvent" class="font-bold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Amount</p>
                    <p id="proofAmount" class="font-bold text-gray-800"></p>
                </div>
            </div>

            <!-- Proof Content -->
            <div id="proofContentContainer" class="bg-gray-100 rounded-lg p-4 flex flex-col items-center justify-center mb-4 overflow-auto max-h-[50vh]">
                <img id="proofImage" src="" alt="Payment Proof" class="max-w-full max-h-96 rounded-lg shadow-md hidden">
                <iframe id="proofPdf" src="" class="w-full h-96 rounded-lg hidden"></iframe>
                <div id="proofPlaceholder" class="text-center py-8">
                    <p class="text-gray-500 font-medium">Loading Proof...</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <a id="downloadProofLink" href="" download class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download
                </a>
                <button onclick="closeProofModal()" class="flex-1 bg-white border border-gray-300 text-gray-700 font-medium py-2 rounded-lg hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openProofModal(title, eventName, amount, proofUrl, fileExt) {
    document.getElementById('proofSubtitle').textContent = title + ' - ' + eventName;
    document.getElementById('proofTitle').textContent = title;
    document.getElementById('proofEvent').textContent = eventName;
    document.getElementById('proofAmount').textContent = 'RM ' + amount;
    document.getElementById('downloadProofLink').href = proofUrl;
    
    const img = document.getElementById('proofImage');
    const pdf = document.getElementById('proofPdf');
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
    
    document.getElementById('proofModal').classList.remove('hidden');
}

function closeProofModal() {
    document.getElementById('proofModal').classList.add('hidden');
}
</script>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection
