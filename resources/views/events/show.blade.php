@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-2xl leading-6 font-medium text-slate-900">
                {{ $event->event_name }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-slate-500">
                {{ $event->event_category }} • {{ $event->status }}
            </p>
        </div>
        <div class="text-right">
            <span class="block text-2xl font-bold text-indigo-600">
                {{ $event->start_date_time->format('d M') }}
            </span>
            <span class="text-sm text-gray-500">{{ $event->start_date_time->format('h:i A') }}</span>
        </div>
    </div>
    <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $event->event_description }}
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
                    @foreach(explode(',', $event->required_skill_tag) as $skill)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                            {{ trim($skill) }}
                        </span>
                    @endforeach
                </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Quota</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $event->assignments->count() }} / {{ $event->quota }} filled
                </dd>
            </div>
        </dl>
    </div>
    
    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end">
        <form action="{{ route('events.apply', $event->id) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Apply Now
            </button>
        </form>
    </div>
</div>

<div class="mt-8">
    <h3 class="text-lg font-medium text-slate-900 mb-4">Recommended Facilitators based on Content Analysis</h3>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
         <div class="flex">
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    See <a href="{{ url('/recommender/debug') }}" class="font-medium underline hover:text-yellow-600">Recommender Dashboard</a> for full debug/demo.
                </p>
            </div>
         </div>
    </div>
</div>
@endsection
