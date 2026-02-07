@extends('layouts.dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8" x-data="{ showModal: false }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Allowance Request</h1>
            <p class="text-gray-500 text-sm">Submit requests for travel, meals, accommodation, and other expenses</p>
        </div>
        <button @click="showModal = true" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Request
        </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Total -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase">Total</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $allowances->count() }}</p>
        </div>
        <!-- Pending -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase text-orange-500">Pending</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $allowances->where('paymentStatus', 'pending')->count() }}</p>
        </div>
        <!-- Approved -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase text-green-500">Approved</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $allowances->where('paymentStatus', 'approved')->count() }}</p>
        </div>
        <!-- Paid -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase text-blue-500">Paid</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $allowances->where('paymentStatus', 'paid')->count() }}</p>
        </div>
        <!-- Total Amount -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase">Total Amount</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₱ {{ number_format($allowances->where('paymentStatus', 'paid')->sum('amount')) }}</p>
        </div>
    </div>

    <!-- Main Content: List -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-gray-800">Your Allowance Requests</h2>
        
        <div class="space-y-4">
            @forelse($allowances as $allowance)
            <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-start gap-4">
                    <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $allowance->title ?? 'Allowance' }}</h3>
                        <p class="text-sm text-gray-500">Event: {{ $allowance->assignment->event->eventName ?? 'Unknown Event' }}</p>
                        
                        <div class="mt-3 space-y-1">
                            <p class="text-sm font-medium text-gray-800">Amount: ₱ {{ number_format($allowance->amount) }}</p>
                            <p class="text-xs text-gray-500">Description: {{ $allowance->description }}</p>
                            <p class="text-xs text-gray-500">Requested: {{ $allowance->paymentDate ? $allowance->paymentDate->format('m/d/Y') : $allowance->created_at->format('m/d/Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full border border-orange-200 uppercase tracking-wide">
                    {{ ucfirst($allowance->paymentStatus) }}
                </span>
            </div>

            @empty
            <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-200">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <p class="text-gray-500">No allowance requests found.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg relative z-10 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">New Allowance Request</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('allowances.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event *</label>
                        <select name="assignment_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                            <option value="">Select Event</option>
                            @foreach($assignments as $assign)
                                <option value="{{ $assign->id ?? $assign->assignmentID }}">{{ $assign->event->eventName ?? 'Event' }} ({{ \Carbon\Carbon::parse($assign->event->startDateTime)->format('M d') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" name="title" placeholder="e.g. Travel Expenses" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₱) *</label>
                        <input type="number" name="amount" placeholder="0.00" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Details about the expense..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-700 hover:bg-green-800 rounded-lg shadow-sm transition-colors">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection
