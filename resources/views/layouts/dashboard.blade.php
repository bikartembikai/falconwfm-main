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
        $sidebarBg = $isManager ? 'bg-[#1a8a5f]' : 'bg-[#3b5eb3]'; 
        // Screenshot exact colors approximation:
        // Green: #1f8b5f approx
        // Blue: #3054b8 approx
    @endphp

    <aside class="w-64 flex-shrink-0 bg-[#2563eb] text-white flex flex-col transition-colors duration-300">
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
             @if($isManager)
                <!-- Manager Nav -->
                <a href="{{ route('events.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('events.*') ? 'bg-white/10' : '' }}">
                    <!-- Icon: 4 squares -->
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Events
                </a>
            @else
                <!-- Facilitator Nav -->
                 <a href="{{ route('events.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full hover:bg-white/10 transition-colors {{ request()->routeIs('events.*') ? 'bg-white/10' : '' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Events
                </a>
            @endif
        </nav>

        <!-- Footer / Logout -->
        <div class="border-t border-white/10 p-4 mb-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-white/10 rounded-md transition-colors">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
