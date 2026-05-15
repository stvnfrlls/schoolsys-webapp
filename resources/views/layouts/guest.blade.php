<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='24' height='24' rx='5' fill='%231e3a5f'/%3E%3Crect x='2.5' y='14.5' width='19' height='3.5' rx='1.5' fill='white' opacity='0.45'/%3E%3Crect x='2.5' y='10' width='19' height='3.5' rx='1.5' fill='white' opacity='0.70'/%3E%3Crect x='2.5' y='5.5' width='19' height='3.5' rx='1.5' fill='white'/%3E%3Ccircle cx='18.5' cy='7.25' r='2' fill='none' stroke='%230ea5e9' stroke-width='1.5'/%3E%3C/svg%3E">

    <title>{{ config('app.name', 'SchoolSys') }} — @yield('title', 'Welcome')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/guest.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="antialiased" style="background: #040d1a; font-family: 'Plus Jakarta Sans', sans-serif;">

    <nav class="nav-dark sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2.5" y="14.5" width="19" height="3.5" rx="1.5" fill="white" opacity="0.45" />
                        <rect x="2.5" y="10" width="19" height="3.5" rx="1.5" fill="white" opacity="0.70" />
                        <rect x="2.5" y="5.5" width="19" height="3.5" rx="1.5" fill="white" />
                        <circle cx="18.5" cy="7.25" r="2" fill="none" stroke="#0ea5e9" stroke-width="1.5" />
                    </svg>
                    <span class="text-sm font-semibold text-white">{{ config('app.name', 'SchoolSys') }}</span>
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ url('/') }}#features"
                        class="text-sm text-slate-400 hover:text-white transition-colors">Features</a>
                    <a href="{{ url('/') }}#about"
                        class="text-sm text-slate-400 hover:text-white transition-colors">About</a>
                    <a href="{{ url('/') }}#contact"
                        class="text-sm text-slate-400 hover:text-white transition-colors">Contact</a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="text-sm font-medium text-sky-400 hover:text-sky-300 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-slate-300 hover:text-white transition-colors">
                            Sign in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white transition-all duration-150"
                                style="background: linear-gradient(135deg, #0ea5e9, #38bdf8); box-shadow: 0 4px 14px rgba(14,165,233,0.30);">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <div class="guest-canvas">
        <div class="dot-grid"></div>
        <div class="guest-orb guest-orb-1"></div>
        <div class="guest-orb guest-orb-2"></div>
        <div class="guest-orb guest-orb-3"></div>

        <main class="guest-slot">
            {{ $slot }}
        </main>
    </div>

    <footer style="background: #020810;" class="text-slate-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start gap-8">

                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2.5" y="14.5" width="19" height="3.5" rx="1.5" fill="white" opacity="0.45" />
                            <rect x="2.5" y="10" width="19" height="3.5" rx="1.5" fill="white" opacity="0.70" />
                            <rect x="2.5" y="5.5" width="19" height="3.5" rx="1.5" fill="white" />
                            <circle cx="18.5" cy="7.25" r="2" fill="none" stroke="#0ea5e9" stroke-width="1.5" />
                        </svg>
                        <span class="text-sm font-semibold text-white">{{ config('app.name', 'SchoolSys') }}</span>
                    </div>
                    <p class="text-xs leading-relaxed max-w-xs" style="color: rgba(148,163,184,0.70);">
                        A comprehensive school management platform built for modern educational institutions.
                    </p>
                </div>

                <div class="flex gap-12 text-sm">
                    <div>
                        <p class="text-white font-semibold mb-3">Product</p>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-white transition-colors text-xs">Features</a></li>
                            <li><a href="#" class="hover:text-white transition-colors text-xs">Pricing</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-white font-semibold mb-3">Support</p>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-white transition-colors text-xs">Documentation</a></li>
                            <li><a href="#" class="hover:text-white transition-colors text-xs">Contact</a></li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="mt-10 pt-6 text-xs" style="border-top: 1px solid rgba(255,255,255,0.07);">
                &copy; {{ date('Y') }} {{ config('app.name', 'SchoolSys') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>