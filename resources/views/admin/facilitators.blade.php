@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Facilitator Management</h1>
        <p class="text-green-600 text-sm">Manage and assign facilitators to events</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <p class="text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex justify-between items-start">
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Facilitators</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalFacilitators }}</p>
            </div>
            <div class="p-2 bg-gray-50 rounded-lg">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex justify-between items-start">
            <div>
                <p class="text-xs text-green-600 font-medium">Available</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $availableFacilitators }}</p>
            </div>
            <div class="p-2 bg-green-50 rounded-lg">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex justify-between items-start">
            <div>
                <p class="text-xs text-yellow-600 font-medium">Busy</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $busyFacilitators }}</p>
            </div>
            <div class="p-2 bg-yellow-50 rounded-lg">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex justify-between items-start">
            <div>
                <p class="text-xs text-red-600 font-medium">Unavailable</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $unavailableFacilitators }}</p>
            </div>
            <div class="p-2 bg-red-50 rounded-lg">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
            <div class="flex-1">
                <label class="text-sm font-medium text-gray-700 mb-1 block">Search</label>
                <div class="relative max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="searchInput" placeholder="Search by name or email" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>
            <div class="flex gap-3 items-end">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Filter by Status</label>
                    <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white min-w-[160px]">
                        <option value="">All Facilitators</option>
                        <option value="available">Available</option>
                        <option value="busy">Busy</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
                <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Facilitator
                </button>
            </div>
        </div>
    </div>

    <!-- Facilitator Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="facilitatorCards">
        @forelse($facilitators as $facilitator)
        @php
            $statusColor = match($facilitator->dynamicStatus) {
                'available' => 'bg-green-100 text-green-700',
                'busy' => 'bg-yellow-100 text-yellow-700',
                'unavailable' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700'
            };
            $eventsAssigned = $facilitator->assignments_count;
            $skillsString = $facilitator->skills->pluck('skillName')->implode(', ');
            $latestComments = $facilitator->reviews->whereNotNull('comments')->where('comments', '!=', '')->sortByDesc('created_at')->take(2);
        @endphp
        <div class="facilitator-card bg-white rounded-xl shadow-sm border border-gray-200 p-6" data-name="{{ strtolower($facilitator->name) }}" data-email="{{ strtolower($facilitator->email) }}" data-status="{{ $facilitator->dynamicStatus }}">
            <!-- Header -->
            <div class="mb-4">
                <h3 class="font-bold text-lg text-gray-900">{{ $facilitator->name }}</h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                        {{ $facilitator->dynamicStatus }}
                    </span>
                    @if($facilitator->statusReason)
                        <span class="text-xs text-gray-500 italic">{{ $facilitator->statusReason }}</span>
                    @endif
                </div>
            </div>

            <!-- Details -->
            <div class="space-y-2 mb-4">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ $facilitator->email }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ $facilitator->yearsOfExperience ?? 0 }} years experience
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    Rating: {{ number_format($facilitator->averageRating ?? 0, 1) }} / 5.0
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    {{ $eventsAssigned }} events assigned
                </div>
            </div>

            <!-- Feedback Comments (Anonymous, Facebook-style) -->
            @if($latestComments->count() > 0)
            <div class="mb-4 border-t border-gray-100 pt-3">
                <p class="text-xs text-gray-500 font-medium mb-2">💬 Recent Feedback</p>
                <div class="space-y-2">
                    @foreach($latestComments as $comment)
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-sm text-gray-700 italic">"{{ Str::limit($comment->comments, 80) }}"</p>
                        <div class="flex items-center justify-between mt-1">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= $comment->rating ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @endfor
                            </div>
                            <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex gap-2 pt-4 border-t border-gray-100">
                <button 
                    onclick="openViewModal(this)"
                    data-facilitator='{!! json_encode([
                        "id" => $facilitator->userID,
                        "name" => $facilitator->name,
                        "email" => $facilitator->email,
                        "expertise" => $facilitator->expertise ?? "Not specified",
                        "experience" => $facilitator->yearsOfExperience ?? 0,
                        "phone" => $facilitator->phone ?? "",
                        "skills" => $skillsString,
                        "status" => $facilitator->dynamicStatus,
                        "rating" => number_format($facilitator->averageRating ?? 0, 1),
                        "events" => $eventsAssigned,
                        "statusReason" => $facilitator->statusReason ?? "",
                        "reviews" => $facilitator->reviews->whereNotNull("comments")->where("comments","!=","")->sortByDesc("created_at")->values()->map(fn($r) => ["comment" => $r->comments, "rating" => $r->rating, "date" => $r->created_at->diffForHumans()])
                    ], JSON_HEX_APOS|JSON_HEX_QUOT) !!}'
                    class="flex-1 flex items-center justify-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    View
                </button>
                <button onclick="openEditModal({{ $facilitator->userID }}, '{{ addslashes($facilitator->name) }}', '{{ $facilitator->email }}', {{ $facilitator->yearsOfExperience ?? 0 }}, '{{ $facilitator->phone ?? '' }}', '{{ addslashes($skillsString) }}', '{{ $facilitator->dynamicStatus }}')" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 border border-green-500 text-green-600 rounded-lg text-sm hover:bg-green-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </button>
                <form action="{{ route('facilitators.destroy', $facilitator->userID) }}" method="POST" onsubmit="return confirm('Delete this facilitator?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center justify-center gap-1 px-3 py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 text-gray-500">
            <p>No facilitators found.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- View Facilitator Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-70 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <h2 class="text-xl font-bold text-gray-800">Facilitator Details</h2>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Full Name</p>
                        <p class="font-semibold text-gray-800" id="viewName"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="font-semibold text-gray-800" id="viewEmail"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <p class="text-xs text-gray-500 mb-1">Experience</p>
                        <p class="font-semibold text-gray-800" id="viewExperience"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Phone Number</p>
                        <p class="font-semibold text-gray-800" id="viewPhone"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Events Assigned</p>
                        <p class="font-semibold text-gray-800" id="viewEvents"></p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-2">Skills</p>
                    <div class="flex flex-wrap gap-2" id="viewSkills"></div>
                </div>
                <div class="flex items-center gap-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Availability Status</p>
                        <span id="viewStatus" class="inline-block px-3 py-1 text-xs font-medium rounded-full"></span>
                    </div>
                    <span id="viewStatusReason" class="text-xs text-gray-500 italic mt-4"></span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Average Rating</p>
                    <p class="font-semibold text-gray-800" id="viewRating"></p>
                </div>

                <!-- Feedback Section -->
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-700 mb-3">💬 Feedback</p>
                    <div id="viewFeedback" class="space-y-3 max-h-[200px] overflow-y-auto pr-1">
                        <p class="text-gray-400 text-sm">No feedback yet.</p>
                    </div>
                </div>

                <!-- Submit Feedback Form -->
                <div class="border-t border-gray-100 pt-4">
                    <form action="{{ route('performance.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="viewFacilitatorId">
                        <p class="text-sm font-semibold text-gray-700 mb-2">📝 Submit Feedback</p>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-600 mb-1">Rating</label>
                            <div class="flex gap-1" id="viewStarRating">
                                @for($s = 1; $s <= 5; $s++)
                                <button type="button" onclick="setViewRating({{ $s }})" class="view-star-btn text-gray-300 hover:text-yellow-400 transition-colors">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="viewRatingInput" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-600 mb-1">Comment</label>
                            <textarea name="comments" rows="2" placeholder="Your feedback..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-lg text-sm">Submit Feedback</button>
                    </form>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeViewModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-lg">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Facilitator Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-70 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Add New Facilitator</h2>
                    <p class="text-gray-500 text-sm">Create a new facilitator account with credentials</p>
                </div>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('facilitators.store') }}" method="POST" class="mt-6">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" required placeholder="Enter full name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" required placeholder="Enter email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" name="password" required placeholder="Enter password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Years of Experience</label>
                        <input type="number" name="yearsOfExperience" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" placeholder="e.g., +63 912 345 6789" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
                        <div class="skill-selector-add w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-2 text-sm focus-within:ring-2 focus-within:ring-green-500 focus-within:border-transparent flex flex-wrap gap-2 items-center relative min-h-[42px]">
                            <div id="addSkillsContainer" class="flex flex-wrap gap-2"></div>
                            <input type="text" id="addSkillInput" class="bg-transparent border-none focus:ring-0 p-0 text-sm flex-1 min-w-[120px] outline-none" placeholder="Type skill & press Enter...">
                            <ul id="addSkillSuggestions" class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-40 overflow-y-auto z-50 hidden"></ul>
                        </div>
                        <input type="hidden" name="skills" id="addHiddenSkills" value="">
                        <p class="text-xs text-gray-400 mt-1">Type to search or add new skills. Press Enter to add.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability Status</label>
                        <select name="availabilityStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="available">Available</option>
                            <option value="busy">Busy</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeAddModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-lg">Add Facilitator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Facilitator Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-70 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Edit Facilitator</h2>
                    <p class="text-gray-500 text-sm">Update facilitator information</p>
                </div>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="editForm" method="POST" class="mt-6">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" id="editName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" id="editEmail" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="editPassword" placeholder="Leave blank to keep current" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Experience (Years/Description)</label>
                            <input type="text" name="experience" id="editExperience" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" id="editPhone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
                        <div class="skill-selector-edit w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-2 text-sm focus-within:ring-2 focus-within:ring-green-500 focus-within:border-transparent flex flex-wrap gap-2 items-center relative min-h-[42px]">
                            <div id="editSkillsContainer" class="flex flex-wrap gap-2"></div>
                            <input type="text" id="editSkillInput" class="bg-transparent border-none focus:ring-0 p-0 text-sm flex-1 min-w-[120px] outline-none" placeholder="Type skill & press Enter...">
                            <ul id="editSkillSuggestions" class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-40 overflow-y-auto z-50 hidden"></ul>
                        </div>
                        <input type="hidden" name="skills" id="editHiddenSkills" value="">
                        <p class="text-xs text-gray-400 mt-1">Type to search or add new skills. Press Enter to add.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability Status</label>
                        <select name="availabilityStatus" id="editStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="available">Available</option>
                            <option value="busy">Busy</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-lg">Update Facilitator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// All available skills from database
const allSkills = {!! json_encode($allSkills ?? ['Event Management', 'Public Speaking', 'Project Management', 'Technical Leadership', 'Workshop Design', 'Team Building']) !!};

// Add modal skills handling
let addCurrentSkills = [];
const addSkillInput = document.getElementById('addSkillInput');
const addSkillsContainer = document.getElementById('addSkillsContainer');
const addHiddenSkills = document.getElementById('addHiddenSkills');
const addSuggestions = document.getElementById('addSkillSuggestions');

// Edit modal skills handling
let editCurrentSkills = [];
const editSkillInput = document.getElementById('editSkillInput');
const editSkillsContainer = document.getElementById('editSkillsContainer');
const editHiddenSkills = document.getElementById('editHiddenSkills');
const editSuggestions = document.getElementById('editSkillSuggestions');

function createSkillChip(skillName, container, skillsArray, hiddenInput, isEdit = false) {
    const chip = document.createElement('div');
    chip.className = 'bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded flex items-center';
    chip.innerHTML = `
        ${skillName}
        <button type="button" class="ml-1.5 text-emerald-600 hover:text-emerald-900 focus:outline-none">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    `;
    
    chip.querySelector('button').addEventListener('click', function() {
        const idx = skillsArray.indexOf(skillName);
        if (idx > -1) skillsArray.splice(idx, 1);
        hiddenInput.value = skillsArray.join(',');
        chip.remove();
    });
    
    container.appendChild(chip);
}

function showSuggestions(input, suggestions, skillsArray) {
    const query = input.value.trim().toLowerCase();
    const filtered = allSkills.filter(s => s.toLowerCase().includes(query) && !skillsArray.includes(s));
    
    suggestions.innerHTML = '';
    if (filtered.length > 0) {
        filtered.forEach(skill => {
            const li = document.createElement('li');
            li.className = 'px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-700';
            li.textContent = skill;
            li.addEventListener('click', () => {
                if (!skillsArray.includes(skill)) {
                    skillsArray.push(skill);
                    const hiddenInput = input === addSkillInput ? addHiddenSkills : editHiddenSkills;
                    const container = input === addSkillInput ? addSkillsContainer : editSkillsContainer;
                    hiddenInput.value = skillsArray.join(',');
                    createSkillChip(skill, container, skillsArray, hiddenInput);
                }
                input.value = '';
                suggestions.classList.add('hidden');
            });
            suggestions.appendChild(li);
        });
        suggestions.classList.remove('hidden');
    } else {
        suggestions.classList.add('hidden');
    }
}

// Add modal input handlers
addSkillInput.addEventListener('input', () => showSuggestions(addSkillInput, addSuggestions, addCurrentSkills));
addSkillInput.addEventListener('focus', () => showSuggestions(addSkillInput, addSuggestions, addCurrentSkills));
addSkillInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const skill = this.value.trim();
        if (skill && !addCurrentSkills.includes(skill)) {
            addCurrentSkills.push(skill);
            addHiddenSkills.value = addCurrentSkills.join(',');
            createSkillChip(skill, addSkillsContainer, addCurrentSkills, addHiddenSkills);
        }
        this.value = '';
        addSuggestions.classList.add('hidden');
    }
});

// Edit modal input handlers
editSkillInput.addEventListener('input', () => showSuggestions(editSkillInput, editSuggestions, editCurrentSkills));
editSkillInput.addEventListener('focus', () => showSuggestions(editSkillInput, editSuggestions, editCurrentSkills));
editSkillInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const skill = this.value.trim();
        if (skill && !editCurrentSkills.includes(skill)) {
            editCurrentSkills.push(skill);
            editHiddenSkills.value = editCurrentSkills.join(',');
            createSkillChip(skill, editSkillsContainer, editCurrentSkills, editHiddenSkills);
        }
        this.value = '';
        editSuggestions.classList.add('hidden');
    }
});

// View Modal
function openViewModal(btn) {
    try {
        console.log('openViewModal called', btn);
        const dataAttr = btn.getAttribute('data-facilitator');
        console.log('data-facilitator attribute:', dataAttr);
        
        const data = JSON.parse(dataAttr);
        console.log('Parsed data:', data);
        
        // Destructure data for easier use (mapping to old param names)
        const { 
            id, name, email, expertise, experience, phone, skills, 
            status, rating, events, statusReason, reviews: feedbackArr 
        } = data;
        
        document.getElementById('viewFacilitatorId').value = id; // Set hidden input for feedback
    document.getElementById('viewName').textContent = name || 'N/A';
    document.getElementById('viewEmail').textContent = email || 'N/A';

    document.getElementById('viewExperience').textContent = experience;
    document.getElementById('viewPhone').textContent = phone || 'N/A';
    document.getElementById('viewEvents').textContent = events;
    document.getElementById('viewRating').textContent = rating + ' / 5.0';
    
    // Skills chips
    const skillsContainer = document.getElementById('viewSkills');
    skillsContainer.innerHTML = '';
    if (skills) {
        skills.split(',').forEach(skill => {
            if (skill.trim()) {
                const chip = document.createElement('span');
                chip.className = 'bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded';
                chip.textContent = skill.trim();
                skillsContainer.appendChild(chip);
            }
        });
    } else {
        skillsContainer.innerHTML = '<span class="text-gray-400 text-xs">No skills listed</span>';
    }
    
    // Status badge
    const statusEl = document.getElementById('viewStatus');
    statusEl.textContent = status;
    statusEl.className = 'inline-block px-3 py-1 text-xs font-medium rounded-full ' + 
        (status === 'available' ? 'bg-green-100 text-green-700' : 
         status === 'busy' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
    
    // Status reason
    document.getElementById('viewStatusReason').textContent = statusReason || '';
    
    // Feedback section
    const feedbackContainer = document.getElementById('viewFeedback');
    feedbackContainer.innerHTML = '';
    if (feedbackArr && feedbackArr.length > 0) {
        feedbackArr.forEach(fb => {
            const div = document.createElement('div');
            div.className = 'bg-gray-50 rounded-lg px-3 py-2';
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += '<svg class="w-3 h-3 ' + (i <= fb.rating ? 'text-yellow-400' : 'text-gray-300') + ' fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
            }
            div.innerHTML = `
                <p class="text-sm text-gray-700 italic">"${fb.comment}"</p>
                <div class="flex items-center justify-between mt-1">
                    <div class="flex">${stars}</div>
                    <span class="text-[10px] text-gray-400">${fb.date}</span>
                </div>
            `;
            feedbackContainer.appendChild(div);
        });
    } else {
        feedbackContainer.innerHTML = '<p class="text-gray-400 text-sm">No feedback yet.</p>';
    }
    
    document.getElementById('viewModal').classList.remove('hidden');
    console.log('Modal opened successfully');
    } catch (error) {
        console.error('Error opening view modal:', error);
        alert('Error opening modal: ' + error.message + '. Please check the browser console for details.');
    }
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

// Star rating for inline feedback form in View modal
function setViewRating(val) {
    document.getElementById('viewRatingInput').value = val;
    document.querySelectorAll('#viewStarRating .view-star-btn').forEach((btn, idx) => {
        if (idx < val) {
            btn.classList.remove('text-gray-300');
            btn.classList.add('text-yellow-400');
        } else {
            btn.classList.remove('text-yellow-400');
            btn.classList.add('text-gray-300');
        }
    });
}

// Add Modal
function openAddModal() {
    addCurrentSkills = [];
    addSkillsContainer.innerHTML = '';
    addHiddenSkills.value = '';
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

// Edit Modal
function openEditModal(id, name, email, experience, phone, skills, status) {
    document.getElementById('editForm').action = '/admin/facilitators/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editExperience').value = experience;
    document.getElementById('editPhone').value = phone;
    document.getElementById('editStatus').value = status;
    
    // Lock availability if busy
    const statusSelect = document.getElementById('editStatus');
    const hiddenStatus = document.getElementById('editHiddenStatus');
    
    if (status === 'busy') {
        statusSelect.disabled = true;
        statusSelect.title = "Cannot change status while Busy (Clashing Event)";
        // Create/Update hidden input if not exists (Assume we add it to HTML or manage locally)
        // Simplest: Check if hidden input exists, if not create it
        if (!hiddenStatus) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'availabilityStatus';
            input.id = 'editHiddenStatus';
            input.value = status;
            document.getElementById('editForm').appendChild(input);
        } else {
            hiddenStatus.value = status;
            hiddenStatus.disabled = false;
        }
    } else {
        statusSelect.disabled = false;
        statusSelect.title = "";
        if (hiddenStatus) {
            hiddenStatus.disabled = true; // Disable so it doesn't conflict
        }
    }
    document.getElementById('editPassword').value = '';
    
    // Setup skills
    editCurrentSkills = skills ? skills.split(',').map(s => s.trim()).filter(s => s) : [];
    editHiddenSkills.value = editCurrentSkills.join(',');
    editSkillsContainer.innerHTML = '';
    editCurrentSkills.forEach(skill => {
        createSkillChip(skill, editSkillsContainer, editCurrentSkills, editHiddenSkills, true);
    });
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterCards);
document.getElementById('statusFilter').addEventListener('change', filterCards);

function filterCards() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    
    document.querySelectorAll('.facilitator-card').forEach(card => {
        const name = card.dataset.name;
        const email = card.dataset.email;
        const cardStatus = card.dataset.status;
        
        const matchesQuery = name.includes(query) || email.includes(query);
        const matchesStatus = !status || cardStatus === status;
        
        card.style.display = (matchesQuery && matchesStatus) ? '' : 'none';
    });
}

// Close modals on backdrop click
document.getElementById('viewModal').addEventListener('click', function(e) { if (e.target === this) closeViewModal(); });
document.getElementById('addModal').addEventListener('click', function(e) { if (e.target === this) closeAddModal(); });
document.getElementById('editModal').addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });
document.getElementById('reviewModal').addEventListener('click', function(e) { if (e.target === this) closeReviewModal(); });

// Hide suggestions on click outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.skill-selector-add')) addSuggestions.classList.add('hidden');
    if (!e.target.closest('.skill-selector-edit')) editSuggestions.classList.add('hidden');
});
</script>
@endsection
