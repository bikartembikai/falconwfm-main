@extends('layouts.dashboard')

@section('content')
<div x-data="eventManager()" class="space-y-8">

    <!-- Modal Backdrop & Container -->
    <div x-show="showModal" 
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity z-0" 
                 aria-hidden="true"
                 @click="showModal = false"></div>

            <!-- Modal Panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full relative z-10"
                 @click.stop>
                
                <!-- Dynamic Content Loaded via AJAX -->
                <div x-html="modalContent" class="p-0">
                    <div class="p-12 text-center text-gray-500">
                        <svg class="animate-spin h-8 w-8 text-gray-400 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading details...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Event Management</h1>
        <p class="text-slate-500">Create and manage events for your organization</p>
    </div>

    <!-- Stats Row (Preserved) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-start">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-4">Total Events</h3>
                <div class="text-3xl font-bold text-slate-900">{{ $totalEventsCount }}</div>
            </div>
            <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>
        <!-- ... (Keeping existing stats logic simplified for brevity in artifact, assuming tool keeps unchanged parts if outside chunks, but tool replaces contiguous block. I must include all stats if I replace the whole section, or target simpler chunks. Since I am wrapping in x-data, I need to indent or replace content block. I will replace the whole file content to be safe and clean) -->
        <!-- Scheduled -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-start">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-4">Scheduled</h3>
                <div class="text-3xl font-bold text-slate-900">{{ $scheduledCount }}</div>
            </div>
            <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <!-- Ongoing -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-start">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-4">Ongoing</h3>
                <div class="text-3xl font-bold text-slate-900">{{ $ongoingCount }}</div>
            </div>
            <div class="p-2 bg-yellow-50 rounded-lg text-yellow-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <!-- Completed -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex justify-between items-start">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-4">Completed</h3>
                <div class="text-3xl font-bold text-slate-900">{{ $completedCount }}</div>
            </div>
            <div class="p-2 bg-green-50 rounded-lg text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('events.index') }}" class="flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="flex-grow w-full md:w-auto">
                <label class="text-xs font-semibold text-slate-500 block mb-1">Search</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                           class="pl-10 block w-full rounded-md border-gray-300 bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border-0 ring-1 ring-inset ring-gray-300">
                </div>
            </div>
            <div class="w-full md:w-48">
                <label class="text-xs font-semibold text-slate-500 block mb-1">Category</label>
                <select name="category" onchange="this.form.submit()" class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-3 ring-1 ring-inset ring-gray-300 sm:text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-48">
                <label class="text-xs font-semibold text-slate-500 block mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-3 ring-1 ring-inset ring-gray-300 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="Upcoming" {{ request('status') == 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="Ongoing" {{ request('status') == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            @if(Auth::user()->role === 'marketing_manager')
            <div class="w-full md:w-auto flex items-end">
                <a href="{{ route('events.create') }}" class="flex items-center justify-center w-full md:w-auto px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#1a8a5f] hover:bg-emerald-700 shadow-sm">
                    Create Event
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Event Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($events as $event)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col h-full hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-bold text-slate-900">{{ $event->eventName }}</h3>
            </div>
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800">{{ $event->eventCategory }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($event->status) }}</span>
            </div>
            
            @if($event->requiredSkills)
            <div class="mb-4">
                <p class="text-xs text-slate-400 mb-1">Required Skills:</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($event->requiredSkills as $skill)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="space-y-3 mb-6 text-sm text-slate-500">
                <div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $event->venue }}</div>
                <div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $event->startDateTime->format('M d, Y, h:i A') }}</div>
                <div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg> {{ $event->assignments->count() }} / {{ $event->quota }} participants</div>
            </div>

            <p class="text-sm text-slate-500 mb-6 line-clamp-2">{{ $event->eventDescription }}</p>

            <div class="mt-auto grid {{ Auth::user()->role === 'marketing_manager' ? 'grid-cols-3' : 'grid-cols-1' }} gap-3">
                @if(Auth::user()->role === 'marketing_manager')
                    <button @click="openModal('{{ $event->eventID }}')" class="flex items-center justify-center px-4 py-2 border border-blue-600 shadow-sm text-sm font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                        View
                    </button>
                    <a href="{{ route('events.edit', $event->eventID) }}" class="flex items-center justify-center px-4 py-2 border border-green-600 shadow-sm text-sm font-medium rounded-md text-green-600 bg-white hover:bg-green-50">Edit</a>
                    <form action="{{ route('events.destroy', $event->eventID) }}" method="POST" class="col-span-1" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 border border-red-600 shadow-sm text-sm font-medium rounded-md text-red-600 bg-white hover:bg-red-50">Delete</button>
                    </form>
                @else
                    <a href="{{ route('events.show', $event->eventID) }}" class="flex items-center justify-center px-4 py-2 border border-blue-600 shadow-sm text-sm font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                        View Details
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-slate-500">No events found.</div>
        @endforelse
    </div>
</div>

<script>
    function eventManager() {
        return {
            showModal: false,
            modalContent: '',
            
            openModal(eventId) {
                this.showModal = true;
                this.modalContent = '<div class="p-12 text-center text-gray-500">Loading...</div>';
                
                fetch(`/events/${eventId}?modal=1`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    this.modalContent = html;
                })
                .catch(error => {
                    this.modalContent = '<div class="p-6 text-red-500 font-medium">Error loading details. Please try again.</div>';
                    console.error('Error:', error);
                });
            }
        }
    }
</script>
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
