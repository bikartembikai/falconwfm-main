@extends('layouts.dashboard')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">My Profile</h1>
        <p class="text-slate-500 mt-1">Manage your facilitator profile information</p>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Profile Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 h-fit text-center">
            <div class="w-32 h-32 mx-auto bg-[#1a8a5f] rounded-full flex items-center justify-center text-4xl font-bold text-white mb-4">
                {{ substr($user->name, 0, 2) }}
            </div>
            
            <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
            <p class="text-slate-500 text-sm mb-4">Facilitator</p>
            
            <div class="flex items-center justify-center space-x-1 text-yellow-500 mb-6">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                <span class="font-bold text-slate-900">{{ number_format($user->averageRating, 1) }}</span>
                <span class="text-slate-400 font-normal">Average Rating</span>
            </div>
            
            <div class="border-t border-slate-100 pt-6">
                <div class="flex items-center justify-center text-slate-600 text-sm">
                    <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    {{ $user->experience }} years experience
                </div>
            </div>
        </div>

        <!-- Right Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Personal Information -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Personal Information
                    </h3>
                    <a href="{{ route('facilitator.editProfile') }}" class="text-sm font-medium text-[#1a8a5f] hover:text-[#15704d] border border-[#1a8a5f] hover:bg-green-50 px-4 py-1.5 rounded-lg transition-colors">
                        Edit Profile
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Full Name</label>
                        <div class="bg-slate-50 rounded-lg px-4 py-2.5 text-slate-900 text-sm font-medium">
                            {{ $user->name }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Email Address</label>
                        <div class="bg-slate-50 rounded-lg px-4 py-2.5 text-slate-900 text-sm font-medium">
                            {{ $user->email }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Phone Number</label>
                        <div class="bg-slate-50 rounded-lg px-4 py-2.5 text-slate-900 text-sm font-medium flex items-center justify-between">
                            {{ $user->phoneNumber ?? 'Not provided' }}
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    Professional Information
                </h3>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Skills</label>
                        <div class="bg-slate-50 rounded-lg p-4 text-sm text-slate-700">
                             @if($user->skills->count() > 0)
                                {{ $user->skills->pluck('skillName')->implode(', ') }}
                             @else
                                No skills listed.
                             @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Years of Experience</label>
                        <div class="bg-slate-50 rounded-lg px-4 py-2.5 text-slate-900 text-sm font-medium">
                            {{ $user->experience ?? '0' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banking Information -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    Banking Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Bank Name</label>
                        <div class="bg-slate-50 rounded-lg px-4 py-2.5 text-slate-900 text-sm font-medium">
                            {{ $user->bankName ?? 'Not provided' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Bank Account Number</label>
                        <div class="bg-slate-50 rounded-lg px-4 py-2.5 text-slate-900 text-sm font-medium">
                            {{ $user->bankAccountNumber ?? 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
