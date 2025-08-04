<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Sistem Peminjaman Lab</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen" x-data="{ mobileMenuOpen: false }">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" @click="mobileMenuOpen = !mobileMenuOpen">
                            <svg class="h-6 w-6" :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="h-6 w-6" :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        
                        <h1 class="text-lg md:text-xl font-bold text-gray-900 ml-2 md:ml-0">
                            <span class="hidden sm:inline">Lab Komputer - {{ auth()->user()->isAdmin() ? 'Admin' : 'Guru' }}</span>
                            <span class="sm:hidden">Lab - {{ auth()->user()->isAdmin() ? 'Admin' : 'Guru' }}</span>
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <span class="text-xs md:text-sm text-gray-600 hidden sm:block">{{ auth()->user()->name }}</span>
                        
                        <!-- Dropdown Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 p-1">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 text-xs text-gray-500 border-b sm:hidden">{{ auth()->user()->name }}</div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Desktop Sidebar -->
            <div class="hidden md:block w-64 bg-white shadow-sm min-h-screen">
                <div class="p-4">
                    <nav class="space-y-2">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.lab-schedule') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.lab-schedule') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                                </svg>
                                Jadwal Lab
                            </a>
                            <a href="{{ route('admin.schedules') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.schedules*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0a2 2 0 01-2 2H7a2 2 0 01-2-2"></path>
                                </svg>
                                Kelola Jadwal Pakem
                            </a>
                            <a href="{{ route('admin.bookings') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.bookings*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Kelola Peminjaman
                            </a>
                        @else
                            <a href="{{ route('guru.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('guru.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                                </svg>
                                Jadwal Lab
                            </a>
                            <a href="{{ route('guru.my-bookings') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('guru.my-bookings') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Riwayat Peminjaman
                            </a>
                        @endif
                    </nav>
                </div>
            </div>

            <!-- Mobile Sidebar -->
            <div x-show="mobileMenuOpen" 
                 @click.away="mobileMenuOpen = false" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 transform -translate-x-full" 
                 x-transition:enter-end="opacity-100 transform translate-x-0" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 transform translate-x-0" 
                 x-transition:leave-end="opacity-0 transform -translate-x-full" 
                 class="md:hidden fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
                <div class="p-4 mt-16">
                    <nav class="space-y-2">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.lab-schedule') }}" @click="mobileMenuOpen = false" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.lab-schedule') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                                </svg>
                                Jadwal Lab
                            </a>
                            <a href="{{ route('admin.schedules') }}" @click="mobileMenuOpen = false" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.schedules*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0a2 2 0 01-2 2H7a2 2 0 01-2-2"></path>
                                </svg>
                                Kelola Jadwal Pakem
                            </a>
                            <a href="{{ route('admin.bookings') }}" @click="mobileMenuOpen = false" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.bookings*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Kelola Peminjaman
                            </a>
                        @else
                            <a href="{{ route('guru.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('guru.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                                </svg>
                                Jadwal Lab
                            </a>
                            <a href="{{ route('guru.my-bookings') }}" @click="mobileMenuOpen = false" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('guru.my-bookings') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Riwayat Peminjaman
                            </a>
                        @endif
                    </nav>
                </div>
            </div>

            <!-- Mobile Overlay -->
            <div x-show="mobileMenuOpen" 
                 @click="mobileMenuOpen = false" 
                 x-transition:enter="transition-opacity ease-linear duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition-opacity ease-linear duration-300" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="md:hidden fixed inset-0 z-40 bg-gray-600 bg-opacity-75"></div>

            <!-- Main Content -->
            <div class="flex-1 p-3 md:p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>