@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-2xl leading-6 font-medium text-slate-900">
                {{ $event->eventName }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-slate-500">
                {{ $event->eventCategory }} • {{ $event->status }}
            </p>
        </div>
        <div class="text-right">
            <span class="block text-2xl font-bold text-indigo-600">
                {{ $event->startDateTime ? $event->startDateTime->format('d M') : 'TBD' }}
            </span>
            <span class="text-sm text-gray-500">
                {{ $event->startDateTime ? $event->startDateTime->format('h:i A') : '' }}
            </span>
        </div>
    </div>
    
    <!-- Event Details -->
    <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $event->eventDescription }}
                </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Venue</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $event->venue ?? 'TBD' }}
                </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Required Skills</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Category: {{ $event->eventCategory }}
                    </span>
                    @if($event->requiredSkills && is_array($event->requiredSkills))
                        @foreach($event->requiredSkills as $skill)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                {{ $skill }}
                            </span>
                        @endforeach
                    @endif
                </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Facilitators Assigned</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $event->assignments->count() }} / {{ $event->quota }}
                    <ul class="mt-2 list-disc list-inside text-sm text-gray-600">
                        @foreach($event->assignments as $assign)
                            <li>{{ $assign->user->name ?? 'Unknown' }}</li>
                        @endforeach
                    </ul>
                </dd>
            </div>
        </dl>
    </div>
    
    <!-- Rule-Based Recommendations & Admin Assign -->
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6 bg-slate-50">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recommended Facilitators (Admin Assign)</h3>
        
        @if(isset($recommendations) && count($recommendations) > 0)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach($recommendations as $rec)
                    <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <div class="flex-shrink-0">
                            <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                {{ substr($rec['name'], 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $rec['name'] }}
                            </p>
                            <p class="text-xs text-gray-500 truncate" title="{{ $rec['matched_keywords'] }}">
                                Matched: {{ $rec['matched_keywords'] }}
                            </p>
                        </div>
                        <div class="z-10">
                             <form action="{{ route('assignments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->eventID }}">
                                <input type="hidden" name="facilitator_id" value="{{ $rec['id'] }}">
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Assign
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            No facilitators found matching the rules for <strong>{{ $event->eventCategory }}</strong>.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
