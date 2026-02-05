<div>
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-lg">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Event Details
        </h3>
        <button type="button" class="js-close-modal text-gray-400 hover:text-gray-500">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="px-6 py-6 bg-white rounded-b-lg">
        <!-- Event Title -->
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
            {{ $event->eventName }}
        </h2>
        
        <!-- Tags -->
        <div class="flex space-x-2 mb-6">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                {{ $event->eventCategory }}
            </span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $event->status === 'upcoming' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                {{ ucfirst($event->status) }}
            </span>
        </div>

        <!-- Blue Requirements Box -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100">
            <h4 class="text-sm font-semibold text-blue-900 mb-2">Category Requirements</h4>
            <ul class="space-y-1">
                <li class="flex text-sm text-blue-800">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 mr-2"></span>
                    <span class="font-medium mr-1">Required Skills:</span> 
                    {{ isset($rule) && !empty($rule->requiredSkill) ? implode(', ', $rule->requiredSkill) : 'None' }}
                </li>
                <li class="flex text-sm text-blue-800">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 mr-2"></span>
                    <span class="font-medium mr-1">Minimum Experience:</span> 
                    {{ $rule->minExperience ?? 0 }} years
                </li>
                <li class="flex text-sm text-blue-800">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 mr-2"></span>
                    <span class="font-medium mr-1">Minimum Rating:</span> 
                    {{ $rule->minRating ?? 0 }}
                </li>
            </ul>
        </div>

        <!-- Description -->
        <p class="text-gray-600 text-sm mb-6 leading-relaxed">
            {{ $event->eventDescription ?? 'No description provided.' }}
        </p>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <!-- Venue -->
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Venue</dt>
                <dd class="text-sm font-semibold text-gray-900 flex items-center">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $event->venue }}
                </dd>
            </div>

            <!-- Participants -->
                <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Participants</dt>
                <dd class="text-sm font-semibold text-gray-900 flex items-center">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ $event->totalParticipants ?? 0 }}
                </dd>
            </div>

            <!-- Start Date -->
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Start Date & Time</dt>
                <dd class="text-sm font-semibold text-gray-900">
                    {{ $event->startDateTime ? $event->startDateTime->format('M d, Y, h:i A') : 'TBD' }}
                </dd>
            </div>

                <!-- End Date -->
                <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">End Date & Time</dt>
                <dd class="text-sm font-semibold text-gray-900">
                    {{ $event->endDateTime ? $event->endDateTime->format('M d, Y, h:i A') : 'TBD' }}
                </dd>
            </div>
        </div>

        <!-- Footer Action -->
        <div class="flex justify-end">
            <button type="button" class="js-close-modal bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Close
            </button>
        </div>
    </div>
</div>
