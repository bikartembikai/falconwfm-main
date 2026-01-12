@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-slate-800">Upcoming Events</h1>
    <a href="{{ route('events.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
        Create Event
    </a>
</div>

<div class="mb-8">
    <form action="{{ route('events.index') }}" method="GET" class="flex gap-4">
        <input type="text" name="search" placeholder="Search events..." value="{{ request('search') }}" 
               class="border-slate-300 border rounded-md px-4 py-2 w-full max-w-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
        
        <select name="category" class="border-slate-300 border rounded-md px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
            <option value="">All Categories</option>
            <option value="Workshop" {{ request('category') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
            <option value="Seminar" {{ request('category') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
            <option value="Conference" {{ request('category') == 'Conference' ? 'selected' : '' }}>Conference</option>
            <option value="Training" {{ request('category') == 'Training' ? 'selected' : '' }}>Training</option>
        </select>

        <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-md hover:bg-slate-700 transition">
            Filter
        </button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($events as $event)
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full font-semibold uppercase tracking-wide">
                        {{ $event->event_category ?? 'Event' }}
                    </span>
                    <span class="text-xs text-slate-500">{{ $event->start_date_time->format('M d, Y') }}</span>
                </div>
                
                <h2 class="text-xl font-bold text-slate-900 mb-2 truncate">{{ $event->event_name }}</h2>
                <p class="text-slate-600 mb-4 line-clamp-2 h-12">{{ $event->event_description }}</p>
                
                <div class="flex items-center text-sm text-slate-500 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    {{ $event->venue ?? 'Online' }}
                </div>

                <div class="border-t border-slate-100 pt-4 flex justify-between items-center">
                    <div class="text-sm">
                        <span class="font-medium text-slate-900">{{ $event->quota }}</span> Spots
                    </div>
                    <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">View Details &rarr;</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-12 text-slate-500">
            No events found matching your criteria.
        </div>
    @endforelse
</div>
@endsection
