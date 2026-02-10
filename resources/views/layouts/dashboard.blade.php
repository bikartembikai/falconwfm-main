<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FalconWFM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full flex overflow-hidden">

    <!-- Sidebar -->
    @php
        $isManager = Auth::user() && Auth::user()->role === 'admin' || Auth::user()->role === 'manager'; // Adjust based on exact role string
        // Based on screenshot: Marketing Manager -> Green Theme
        // Facilitator -> Blue Theme
        $sidebarBg = $isManager ? 'bg-[#1a8a5f]' : (Auth::user()->role === 'facilitator' ? 'bg-[#1a8a5f]' : 'bg-[#3b5eb3]'); 
        // Marketing/Ops -> Greenish? Facilitator -> Green (as per new image) allowing Ops to stay Blue or Green?
        // New Image shows Facilitator = Green. Let's make Facilitator Green (#1a8a5f).
        // Let's assume Marketing is Blue now or keeping previous logic? 
        // Simplification: Facilitator = Green. Others = Blue (default) or keep existing.
        if (Auth::user()->role === 'facilitator') {
            $sidebarBg = 'bg-[#1a8a5f]';
        } elseif (Auth::user()->role === 'operation_manager') {
             $sidebarBg = 'bg-[#2563eb]'; // Keep Blue for Ops
        }
    @endphp

    <!-- Mobile Menu Button -->
    <button id="mobileSidebarToggle" class="fixed top-4 left-4 z-50 lg:hidden bg-white shadow-lg rounded-lg p-2 text-gray-700">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <aside id="mobileSidebar" class="w-64 flex-shrink-0 {{ $sidebarBg }} text-white flex flex-col transition-all duration-300 fixed lg:static inset-y-0 left-0 z-40 -translate-x-full lg:translate-x-0">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 font-bold text-xl tracking-tight">
            FalconWFM
        </div>

        <!-- User Profile (Sidebar Header - Figma Style) -->
        <div class="px-6 py-8">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded bg-white/20 flex items-center justify-center text-sm font-bold uppercase">
                    {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
                </div>
                <div>
                     <!-- Figma shows "Amir Afham (Marketing)" -->
                     <!-- We can use the Name directly or append role if needed for display -->
                    <div class="font-bold text-sm leading-tight">{{ Auth::user()->name ?? 'Guest' }}</div>
                    <div class="text-xs text-blue-200 mt-0.5">{{ Auth::user()->role ?? 'User' }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 space-y-2 mt-4">
             @if(Auth::user()->role === 'operation_manager')
                <!-- Operation Manager Specific Nav -->
                <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('facilitators.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('facilitators.*') ? 'bg-white/10' : '' }}">
                     <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Facilitators
                </a>

                <a href="{{ route('events.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('events.*') ? 'bg-white/10' : '' }}">
                     <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Assignments
                </a>

                <a href="{{ route('admin.leaves') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('admin.leaves') ? 'bg-white/10' : '' }}">
                     <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Leave Management
                </a>

                <a href="{{ route('admin.attendance') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('admin.attendance') ? 'bg-white/10' : '' }}">
                     <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Attendance
                </a>



                 <a href="{{ route('admin.payroll') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('admin.payroll') ? 'bg-white/10' : '' }}">
                     <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Payroll
                </a>

             @elseif($isManager)
                <!-- Other Manager Nav (e.g. Admin/Marketing) - Keep default -->
                <a href="{{ route('events.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('events.*') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Events
                </a>
            @elseif(Auth::user()->role === 'marketing_manager')
                <!-- Marketing Manager Nav -->
                 <a href="{{ route('events.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('events.*') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Event Listing
                </a>
            @else
                <!-- Facilitator Nav -->
                 <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                 <a href="{{ route('assignments.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('assignments.*') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Event Assignments
                </a>
                <a href="{{ route('attendance.clockin_view') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('attendance.*') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Clock In/Out
                </a>
                <a href="{{ route('facilitator.performance') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('facilitator.performance') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    Performance Reviews
                </a>

                <a href="{{ route('leaves.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('leaves.*') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Leave Request
                </a>
                <a href="{{ route('facilitator.payments') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('facilitator.payments*') ? 'bg-white/10' : '' }}">
                     <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Payments
                </a>
                <a href="{{ route('facilitator.history') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('facilitator.history') ? 'bg-white/10' : '' }}">
                     <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Past Events
                </a>
            @endif
        </nav>

        <!-- Footer / Logout -->
        <div class="mt-auto border-t border-white/10 p-6">
            @if(Auth::user()->role === 'facilitator')
                <div class="mb-4">
                    <div class="text-xs text-white/60 mb-1">Logged in as</div>
                    <div class="font-bold text-sm">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-white/60">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
                
                <a href="{{ route('facilitator.profile') }}" class="flex items-center justify-center w-full px-3 py-2 text-sm font-medium bg-white text-[#1a8a5f] hover:bg-gray-100 rounded-md transition-colors mb-2">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    My Profile
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-3 py-2 text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 rounded-md transition-colors justify-center {{ Auth::user()->role === 'facilitator' ? '' : '' }}">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto bg-slate-50">
        <div class="px-8 py-8">
            @yield('content')
        </div>
    </main>

</body>
</html>
