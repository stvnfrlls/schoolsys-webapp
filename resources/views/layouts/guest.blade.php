<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SchoolSys') }} — @yield('title', 'Welcome')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-slate-50 antialiased">

    {{-- ===================== NAVBAR ===================== --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-school-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                            <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-800">{{ config('app.name', 'SchoolSys') }}</span>
                </a>

                {{-- Nav links --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="#features" class="text-sm text-slate-500 hover:text-slate-800 transition-colors">Features</a>
                    <a href="#about" class="text-sm text-slate-500 hover:text-slate-800 transition-colors">About</a>
                    <a href="#contact" class="text-sm text-slate-500 hover:text-slate-800 transition-colors">Contact</a>
                </div>

                {{-- Auth links --}}
                <div class="flex items-center gap-3">
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="text-sm font-medium text-school-800 hover:text-school-600 transition-colors">
                        Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                        Sign in
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Get Started
                    </a>
                    @endif
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    {{-- ===================== CONTENT ===================== --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="bg-school-900 text-slate-400 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-sky-500 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-white">{{ config('app.name', 'SchoolSys') }}</span>
                    </div>
                    <p class="text-xs leading-relaxed max-w-xs">A comprehensive school management platform built for modern educational institutions.</p>
                </div>
                <div class="flex gap-12 text-sm">
                    <div>
                        <p class="text-white font-medium mb-3">Product</p>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-white font-medium mb-3">Support</p>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-white transition-colors">Documentation</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10 mt-10 pt-6 text-xs">
                &copy; {{ date('Y') }} {{ config('app.name', 'SchoolSys') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>