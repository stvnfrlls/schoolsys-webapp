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
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /*
         * app.blade.php design tokens — keeps the dashboard visually
         * consistent with the landing page's color system and typography.
         */

        /* ── Base ── */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── Sidebar: active item glow ── */
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.13);
            color: #ffffff;
            box-shadow: inset 3px 0 0 #38bdf8;
            /* sky accent on active */
        }

        /* ── Top bar gradient accent line ── */
        .topbar-accent {
            position: relative;
        }

        .topbar-accent::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(14, 165, 233, 0.25), transparent);
        }

        /* ── Page content fade-in ── */
        .page-fade {
            animation: pageFadeIn 0.25s ease both;
        }

        @keyframes pageFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Notification dot pulse ── */
        .notif-dot {
            animation: notifPulse 2.4s ease-in-out infinite;
        }

        @keyframes notifPulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.5);
            }
        }
    </style>

    @stack('styles')
</head>

{{--
Light slate body — matches the white/light sections of the landing page
(stats, role-cards, features). Contrasts cleanly with the dark sidebar.
--}}

<body class="bg-slate-50 antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- ===================== SIDEBAR ===================== --}}
        {{--
        Unchanged structure; minor visual tweak: sky-500 logo bg
        and active link glow (via .sidebar-link.active override above)
        now both reference the same sky/school color system as the landing page.
        --}}
        <aside id="sidebar" class="flex flex-col w-64 shrink-0 bg-school-800 text-white transition-transform duration-200 ease-in-out
                   fixed inset-y-0 left-0 z-40
                   -translate-x-full md:relative md:translate-x-0">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2.5" y="14.5" width="19" height="3.5" rx="1.5" fill="white" opacity="0.45"></rect>
                    <rect x="2.5" y="10" width="19" height="3.5" rx="1.5" fill="white" opacity="0.70"></rect>
                    <rect x="2.5" y="5.5" width="19" height="3.5" rx="1.5" fill="white"></rect>
                    <circle cx="18.5" cy="7.25" r="2" fill="none" stroke="#0ea5e9" stroke-width="1.5"></circle>
                </svg>
                <div>
                    <p class="text-sm font-semibold leading-tight">{{ config('app.name', 'SchoolSys') }}</p>
                    <p class="text-xs text-slate-400 leading-tight">Management System</p>
                </div>
            </div>

            {{-- Navigation --}}
            @php
                $activeGroup = null;
                foreach (config('sidebar') as $group) {
                    foreach ($group['items'] as $item) {
                        if (request()->routeIs(str_replace('.index', '.*', $item['route']))) {
                            $activeGroup = $group['section'];
                            break 2;
                        }
                    }
                }
            @endphp

            <nav x-data="{ openGroup: '{{ $activeGroup }}' }" class="flex-1 overflow-y-auto px-3 py-4 space-y-2">

                @foreach(config('sidebar') as $group)
                    @if(!empty($group['collapsible']))
                        <div>
                            <button
                                @click="openGroup = openGroup === '{{ $group['section'] }}' ? null : '{{ $group['section'] }}'"
                                class="w-full flex justify-between items-center px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                                <span class="font-medium tracking-wide text-xs uppercase">{{ $group['section'] }}</span>
                                <span x-text="openGroup === '{{ $group['section'] }}' ? '−' : '+'"></span>
                            </button>

                            <div x-show="openGroup === '{{ $group['section'] }}'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1" class="space-y-0.5 overflow-hidden">
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
                    {{-- Avatar: gradient matches sky palette --}}
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                        style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Sidebar overlay (mobile) --}}
        <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 hidden md:hidden backdrop-blur-sm"
            onclick="toggleSidebar()">
        </div>

        {{-- ===================== MAIN CONTENT ===================== --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            {{-- Top bar --}}
            <header
                class="topbar-accent flex items-center justify-between h-16 px-6 bg-white border-b border-slate-200 shrink-0">
                <div class="flex items-center gap-4">
                    {{-- Mobile sidebar toggle --}}
                    <button onclick="toggleSidebar()"
                        class="md:hidden text-slate-400 hover:text-slate-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    {{-- Page title --}}
                    <div>
                        <h1 class="text-base font-semibold text-slate-800"
                            style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            @yield('page-title', 'Dashboard')
                        </h1>
                        @hasSection('breadcrumb')
                            <p class="text-xs text-slate-400 mt-0.5">@yield('breadcrumb')</p>
                        @endif
                    </div>
                </div>

                {{-- Right side actions --}}
                <div class="flex items-center gap-3">
                    {{-- Notifications --}}
                    <button
                        class="relative p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span class="notif-dot absolute top-1 right-1 w-2 h-2 bg-sky-500 rounded-full"></span>
                    </button>

                    {{-- Divider --}}
                    <div class="w-px h-5 bg-slate-200"></div>

                    {{-- Avatar: same gradient as sidebar avatar --}}
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold cursor-pointer"
                        style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);" title="{{ Auth::user()->name }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6 page-fade">
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