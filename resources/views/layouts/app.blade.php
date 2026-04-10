<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SchoolSys') }} — @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-slate-100 antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- ===================== SIDEBAR ===================== --}}
        <aside
            id="sidebar"
            class="flex flex-col w-64 shrink-0 bg-school-800 text-white transition-transform duration-200 ease-in-out
               fixed inset-y-0 left-0 z-40
               -translate-x-full md:relative md:translate-x-0">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-sky-500 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                        <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold leading-tight">{{ config('app.name', 'SchoolSys') }}</p>
                    <p class="text-xs text-slate-400 leading-tight">Management System</p>
                </div>
            </div>

            {{-- Navigation --}}
            @php
                $activeGroup = null;
                foreach(config('sidebar') as $group) {
                    foreach($group['items'] as $item) {
                        if(request()->routeIs(str_replace('.index', '.*', $item['route']))) {
                            $activeGroup = $group['section'];
                            break 2;
                        }
                    }
                }
            @endphp

            <nav x-data="{ openGroup: '{{ $activeGroup }}' }"
                class="flex-1 overflow-y-auto px-3 py-4 space-y-2">

                @foreach(config('sidebar') as $group)
                    @if(!empty($group['collapsible']))
                        <div>
                            <button
                                @click="openGroup = openGroup === '{{ $group['section'] }}' ? null : '{{ $group['section'] }}'"
                                class="w-full flex justify-between items-center px-4 py-2 text-sm text-slate-300 hover:text-white">

                                <span>{{ $group['section'] }}</span>
                                <span x-text="openGroup === '{{ $group['section'] }}' ? '-' : '+'"></span>
                            </button>

                            <div
                                x-show="openGroup === '{{ $group['section'] }}'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="space-y-0.5 overflow-hidden">
                                @endif

                                @foreach($group['items'] as $item)
                                @if(!$item['permission'] || auth()->user()->can($item['permission']))
                                <a href="{{ $item['route'] != '#' ? route($item['route']) : '#' }}"
                                    class="sidebar-link {{ !empty($item['child']) ? 'ml-4' : '' }} {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'active' : '' }}">
                                    {{ $item['label'] }}
                                </a>
                                @endif
                                @endforeach

                                @if(!empty($group['collapsible']))
                            </div>
                        </div>
                    @endif
                @endforeach

            </nav>

            {{-- User / Logout --}}
            <div class="border-t border-white/10 px-4 py-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-sky-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Sidebar overlay (mobile) --}}
        <div id="sidebar-overlay"
            class="fixed inset-0 z-30 bg-black/50 hidden md:hidden"
            onclick="toggleSidebar()">
        </div>

        {{-- ===================== MAIN CONTENT ===================== --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            {{-- Top bar --}}
            <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-200 shrink-0">
                <div class="flex items-center gap-4">
                    {{-- Mobile sidebar toggle --}}
                    <button onclick="toggleSidebar()"
                        class="md:hidden text-slate-500 hover:text-slate-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    {{-- Page title --}}
                    <div>
                        <h1 class="text-base font-semibold text-slate-800 font-sans">@yield('page-title', 'Dashboard')</h1>
                        @hasSection('breadcrumb')
                        <p class="text-xs text-slate-400 mt-0.5">@yield('breadcrumb')</p>
                        @endif
                    </div>
                </div>

                {{-- Right side actions --}}
                <div class="flex items-center gap-3">
                    {{-- Notifications --}}
                    <button class="relative text-slate-500 hover:text-slate-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-sky-500 rounded-full"></span>
                    </button>

                    {{-- Avatar --}}
                    <div class="w-8 h-8 rounded-full bg-school-700 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>

        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>

    @stack('scripts')
</body>

</html>