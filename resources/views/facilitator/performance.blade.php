@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Performance Reviews</h1>
        <p class="text-gray-500 text-sm">Rate and provide feedback for co-facilitators from recent events</p>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Total Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalReviews }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Pending Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingReviews }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Completed Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $completedReviews }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Events List -->
    <div class="space-y-6">
        @forelse($completedEvents as $event)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $event->eventName }}</h3>
                        <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::parse($event->startDateTime)->format('F d, Y') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $event->venue ?? 'TBA' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-4">
                @forelse($event->assignments as $assignment)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ substr($assignment->user->name, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $assignment->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $assignment->user->email }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($assignment->reviews->isNotEmpty())
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Reviewed
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-orange-50 text-orange-700 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pending
                        </span>
                        <button onclick="openReviewModal({{ $assignment->assignmentID }}, '{{ addslashes($assignment->user->name) }}', '{{ addslashes($event->eventName) }}', '{{ addslashes($event->venue ?? 'TBA') }}')" 
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Submit Review
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">No co-facilitators in this event.</p>
                @endforelse
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-200">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <p class="text-gray-500">No completed events found. Complete events to review your co-facilitators.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 px-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Submit Performance Review</h3>
                <p class="text-sm text-gray-500">Rate and provide feedback for your co-facilitator</p>
            </div>
            <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-sm" id="modalFacilitatorInitials"></div>
                <div>
                    <p class="font-semibold text-gray-900" id="modalFacilitatorName"></p>
                    <p class="text-xs text-gray-500">Co-Facilitator</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span id="modalEventName"></span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 mt-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span id="modalEventVenue"></span>
            </div>
        </div>

        <form action="{{ route('facilitator.performance.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="assignment_id" id="modalAssignmentId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating *</label>
                    <div class="flex gap-2" id="starRating">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" onclick="setRating({{ $i }})" class="star-btn text-gray-300 hover:text-yellow-400 transition-colors">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" value="0" required>
                    <p class="text-xs text-gray-500 mt-1">Click on stars to rate</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comments *</label>
                    <textarea name="comments" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Share your observations about this facilitator's performance, strengths, and areas for improvement..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Provide constructive feedback to help your colleague grow professionally</p>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r-lg">
                    <p class="text-xs font-semibold text-blue-700 mb-1">Review Guidelines</p>
                    <ul class="text-xs text-blue-600 space-y-1">
                        <li>• Be honest and constructive in your feedback</li>
                        <li>• Focus on specific behaviors and examples</li>
                        <li>• Consider teamwork, communication, and professionalism</li>
                        <li>• Provide suggestions for improvement when applicable</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeReviewModal()" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm transition-colors">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReviewModal(assignmentId, name, eventName, venue) {
    document.getElementById('modalAssignmentId').value = assignmentId;
    document.getElementById('modalFacilitatorName').textContent = name;
    document.getElementById('modalFacilitatorInitials').textContent = name.substring(0, 2).toUpperCase();
    document.getElementById('modalEventName').textContent = eventName;
    document.getElementById('modalEventVenue').textContent = venue;
    document.getElementById('reviewModal').classList.remove('hidden');
    setRating(0); // Reset rating
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

function setRating(rating) {
    document.getElementById('ratingInput').value = rating;
    const stars = document.querySelectorAll('.star-btn');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.add('text-gray-300');
            star.classList.remove('text-yellow-400');
        }
    });
}

// Close modal on backdrop click
document.getElementById('reviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeReviewModal();
});
</script>
@endsection
