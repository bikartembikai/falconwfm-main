@extends('layouts.dashboard')

@section('content')
<div class="space-y-8" x-data="{ showReviewModal: false, selectedFacilitator: null, selectedEvent: null, reviewTargetId: null, reviewTargetName: null, reviewEventName: null, reviewEventId: null }">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Performance Reviews</h1>
        <p class="text-slate-500 mt-1">Rate and provide feedback for co-facilitators from recent events</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Reviews -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-1">Total Reviews</h3>
                <div class="text-3xl font-bold text-slate-900">{{ $totalReviews }}</div>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-1">Pending Reviews</h3>
                <div class="text-3xl font-bold {{ $pendingCount > 0 ? 'text-orange-600' : 'text-slate-900' }}">{{ $pendingCount }}</div>
            </div>
            <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Completed Reviews -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-1">Completed Reviews</h3>
                <div class="text-3xl font-bold text-green-600">{{ $completedCount }}</div>
            </div>
            <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Pending Reviews List -->
    <div>
        <h2 class="text-lg font-bold text-slate-900 mb-4">Pending Reviews</h2>
        @if(count($pendingReviews) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 divide-y divide-slate-100">
                @foreach($pendingReviews as $assign)
                    <div class="p-6 flex items-center justify-between group hover:bg-slate-50 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">
                                {{ substr($assign->user->name, 0, 2) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $assign->user->name }}</h3>
                                <p class="text-sm text-slate-500">{{ $assign->event->eventName }} • {{ $assign->event->startDateTime->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-1 text-xs font-medium bg-orange-100 text-orange-700 rounded-full">Pending</span>
                            <button 
                                @click="showReviewModal = true; reviewTargetId = '{{ $assign->user->userID }}'; reviewTargetName = '{{ $assign->user->name }}'; reviewEventName = '{{ $assign->event->eventName }}'; reviewEventId = '{{ $assign->event->eventID }}'"
                                class="bg-[#1a8a5f] hover:bg-[#15704d] text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                                Submit Review
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center text-slate-500">
                <p>You have no pending reviews. Great job!</p>
            </div>
        @endif
    </div>

    <!-- Completed Reviews History -->
    @if(count($completedReviewsList) > 0)
    <div>
        <h2 class="text-lg font-bold text-slate-900 mb-4">Completed Reviews</h2>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 divide-y divide-slate-100">
            @foreach($completedReviewsList as $assign)
                <div class="p-6 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="h-10 w-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold border border-green-200">
                            {{ substr($assign->user->name, 0, 2) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $assign->user->name }}</h3>
                            <p class="text-sm text-slate-500">{{ $assign->event->eventName }} • Rated: {{ number_format($assign->review->rating, 1) }} ★</p>
                        </div>
                    </div>
                    <div>
                        <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submitted
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Review Modal -->
    <div x-show="showReviewModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showReviewModal = false"></div>
        
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg relative z-10 p-6">
            <h3 class="text-xl font-bold text-slate-900 mb-1">Review <span x-text="reviewTargetName"></span></h3>
            <p class="text-sm text-slate-500 mb-6">Event: <span x-text="reviewEventName"></span></p>

            <form :action="'{{ route('reviews.store', 'PH') }}'.replace('PH', reviewTargetId)" method="POST">
                @csrf
                <input type="hidden" name="event_id" :value="reviewEventId">

                <!-- Rating -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Rating</label>
                    <div class="flex space-x-2" x-data="{ rating: 0 }">
                        <template x-for="i in 5">
                            <button type="button" @click="rating = i" class="focus:outline-none transition-colors duration-200">
                                <svg class="w-8 h-8" :class="rating >= i ? 'text-yellow-400 fill-current' : 'text-slate-200 fill-current'" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                            </button>
                        </template>
                        <input type="hidden" name="rating" x-model="rating">
                    </div>
                </div>

                <!-- Feedback -->
                <div class="mb-6">
                    <label for="feedback" class="block text-sm font-medium text-slate-700 mb-1">Feedback Comments</label>
                    <textarea id="feedback" name="feedback_comments" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Share your experience working with this facilitator..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showReviewModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#1a8a5f] hover:bg-[#15704d] rounded-lg shadow-sm transition-colors">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alpine.js is assumed to be loaded via app.js or CDN. If not, add CDN here just in case. -->
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
