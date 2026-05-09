<x-guest-layout>
    @section('title', 'Welcome')

    @push('styles')
        @vite(['resources/css/welcome.css'])
    @endpush

    {{-- HERO --}}
    <section class="hero-section noise-overlay"
        style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding-top: 4rem; padding-bottom: 4rem;">
        <div class="dot-grid absolute inset-0"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full" style="z-index: 1;">
            <div class="max-w-4xl mx-auto text-center">

                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border mb-4"
                    style="background: rgba(14,165,233,0.08); border-color: rgba(56,189,248,0.25);">
                    <span class="badge-dot flex-shrink-0"></span>
                    <span class="text-xs font-bold text-sky-300 tracking-widest uppercase">Student Management
                        System</span>
                </div>

                <h1 class="font-bold mb-8"
                    style="font-size: clamp(2rem, 5vw, 3.5rem); line-height: 1.06; -webkit-text-fill-color: unset; letter-spacing: -0.02em;">
                    <span class="gradient-text" style="display: block;">The smarter way</span>
                    <span class="text-white" style="display: block;">to run your school.</span>
                </h1>

                <p class="text-slate-400 text-lg sm:text-xl max-w-2xl mx-auto mb-8 leading-relaxed"
                    style="font-weight: 300; margin-top: 0;">
                    From enrollment to graduation — manage students, faculty, schedules, and grades from one beautifully
                    unified platform built for modern institutions.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="btn-primary inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl text-white font-semibold text-sm">
                            Get Started — It's Free
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    @endif
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-sm font-medium text-slate-300 hover:text-white transition-colors"
                        style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H9" />
                        </svg>
                        Sign In
                    </a>
                </div>

                <div id="role-pills" class="flex flex-wrap items-center justify-center gap-3"></div>

            </div>
        </div>
    </section>

    {{-- STATS --}}
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: 4rem; padding-bottom: 3rem;">
            <dl id="stats-grid" class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center"></dl>
        </div>
    </section>

    {{-- ROLE CARDS --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <p class="text-sky-500 text-xs font-bold tracking-widest uppercase mb-3">Tailored for everyone</p>
                <h2 class="text-3xl font-bold text-slate-900 mb-4">One platform. Every role.</h2>
                <p class="text-slate-500 max-w-xl mx-auto leading-relaxed">Each user sees exactly what they need — no
                    clutter, no confusion. Fine-grained role control from day one.</p>
            </div>
            <div id="role-cards-grid" class="grid sm:grid-cols-2 md:grid-cols-3 gap-6"></div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="py-24" style="background: #f8fafc;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <p class="text-sky-500 text-xs font-bold tracking-widest uppercase mb-3">Platform Features</p>
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Everything your school needs</h2>
                <p class="text-slate-500 max-w-xl mx-auto leading-relaxed">A complete toolkit for administrators,
                    faculty, and students — no external tools required.</p>
            </div>
            <div id="features-grid" class="grid sm:grid-cols-2 md:grid-cols-3 gap-6"></div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="py-24 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <p class="text-sky-500 text-xs font-bold tracking-widest uppercase mb-3">Getting Started</p>
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Up and running in minutes</h2>
                <p class="text-slate-500 max-w-lg mx-auto">No long onboarding. No training sessions. Create your account
                    and your institution is live.</p>
            </div>
            <div id="steps-container"></div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-section py-28">
        <div class="cta-orb"></div>
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 text-center" style="z-index: 1;">

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border mb-8"
                style="background: rgba(14,165,233,0.08); border-color: rgba(56,189,248,0.25);">
                <span class="badge-dot flex-shrink-0"></span>
                <span class="text-xs font-bold text-sky-300 tracking-widest uppercase">Ready to start?</span>
            </div>

            <h2 class="font-bold text-white mb-6"
                style="font-size: clamp(2.5rem, 6vw, 4.5rem); line-height: 1.1; letter-spacing: -0.02em;">
                Modernize your school<br>
                <span class="gradient-text-sky">starting today.</span>
            </h2>

            <p class="text-slate-400 mb-10 leading-relaxed max-w-xl mx-auto">
                Join forward-thinking institutions that have replaced spreadsheets and paperwork with a single, reliable
                platform.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="btn-primary inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-white font-semibold text-sm">
                        Create your free account
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @endif
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-8 py-4 rounded-xl text-sm font-medium text-slate-300 hover:text-white transition-colors"
                    style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                    Sign in to your account
                </a>
            </div>

        </div>
    </section>

    @push('scripts')
        @vite(['resources/js/welcome.js'])
    @endpush

</x-guest-layout>