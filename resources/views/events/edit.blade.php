@extends('layouts.dashboard')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Edit Event</h1>
        <p class="text-slate-500 text-sm mt-1">Update event details.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('events.update', $event->eventID) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Event Name -->
                <div class="col-span-2">
                    <label for="eventName" class="block text-sm font-medium text-slate-700 mb-1">Event Name</label>
                    <input type="text" name="eventName" id="eventName" value="{{ old('eventName', $event->eventName) }}" required
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('eventName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="eventCategory" class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <select name="eventCategory" id="eventCategory" required
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('eventCategory', $event->eventCategory) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('eventCategory') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Venue -->
                <div>
                    <label for="venue" class="block text-sm font-medium text-slate-700 mb-1">Venue</label>
                    <input type="text" name="venue" id="venue" value="{{ old('venue', $event->venue) }}" required
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('venue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="eventDescription" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="eventDescription" id="eventDescription" rows="4"
                    class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('eventDescription', $event->eventDescription) }}</textarea>
                @error('eventDescription') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Skills Display (Auto-populated from Category) -->
            <div x-data="categorySkills()" x-init="$watch('selectedCategory', value => updateSkills(value))">
                <input type="hidden" x-model="selectedCategory">
                
                <label class="block text-sm font-medium text-slate-700 mb-1">Required Skills (Auto-determined by Category)</label>
                <div class="p-3 border border-slate-200 rounded-md bg-slate-50 min-h-[42px]">
                    <template x-if="displaySkills.length > 0">
                        <div class="flex flex-wrap gap-2">
                             <template x-for="skill in displaySkills" :key="skill">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="skill"></span>
                            </template>
                        </div>
                    </template>
                    <template x-if="displaySkills.length === 0">
                         <span class="text-sm text-slate-400 italic">Select a category to view required skills.</span>
                    </template>
                </div>
            </div>

            <!-- Logistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Participants -->
                <div>
                    <label for="totalParticipants" class="block text-sm font-medium text-slate-700 mb-1">Total Participants</label>
                    <input type="number" name="totalParticipants" id="totalParticipants" value="{{ old('totalParticipants', $event->totalParticipants) }}" min="1" required
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('totalParticipants') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Facilitators Needed -->
                <div>
                    <label for="quota" class="block text-sm font-medium text-slate-700 mb-1">Facilitators Needed</label>
                    <input type="number" name="quota" id="quota" value="{{ old('quota', $event->quota) }}" min="1" required
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('quota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <label for="startDateTime" class="block text-sm font-medium text-slate-700 mb-1">Start Date & Time</label>
                    <input type="datetime-local" name="startDateTime" id="startDateTime" 
                           value="{{ old('startDateTime', $event->startDateTime ? $event->startDateTime->format('Y-m-d\TH:i') : '') }}" required
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('startDateTime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="endDateTime" class="block text-sm font-medium text-slate-700 mb-1">End Date & Time</label>
                    <input type="datetime-local" name="endDateTime" id="endDateTime" 
                           value="{{ old('endDateTime', $event->endDateTime ? $event->endDateTime->format('Y-m-d\TH:i') : '') }}"
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('endDateTime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('events.index') }}" class="px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function categorySkills() {
        return {
            rules: @json($eventRules),
            selectedCategory: '{{ old('eventCategory', $event->eventCategory) }}',
            displaySkills: [],

            init() {
                // Hook into the select change manually
                const select = document.getElementById('eventCategory');
                select.addEventListener('change', (e) => {
                    this.updateSkills(e.target.value);
                });
                
                // Initial update
                if(this.selectedCategory) {
                    this.updateSkills(this.selectedCategory);
                }
            },

            updateSkills(category) {
                if (this.rules[category] && this.rules[category].requiredSkill) {
                    this.displaySkills = this.rules[category].requiredSkill;
                } else {
                    this.displaySkills = [];
                }
            }
        }
    }
</script>
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
