<x-guest-layout>
    @section('title', 'Welcome')

    {{-- ===================== HERO ===================== --}}
    <section class="relative bg-school-900 overflow-hidden">
        {{-- Background pattern --}}
        <div class="absolute inset-0 opacity-5"
            style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;">
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-school-900 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                </svg>
                Modern School Management
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-bold text-white leading-tight mb-6">
                Everything your school<br>
                <span class="text-sky-400">needs in one place</span>
            </h1>

            <p class="text-slate-400 text-lg max-w-2xl mx-auto mb-10 font-light">
                Streamline enrollment, scheduling, grading, and attendance for your entire institution — from a single, unified dashboard.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-sky-500 text-white font-semibold text-sm hover:bg-sky-400 transition-colors shadow-lg shadow-sky-500/25">
                    Get Started Free
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                @endif
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl border border-white/20 text-white text-sm font-medium hover:bg-white/10 transition-colors">
                    Sign In
                </a>
            </div>
        </div>
    </section>

    {{-- ===================== STATS ===================== --}}
    <section class="bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <dt class="text-3xl font-bold text-school-800 font-serif">2,400+</dt>
                    <dd class="text-sm text-slate-500 mt-1">Students Managed</dd>
                </div>
                <div>
                    <dt class="text-3xl font-bold text-school-800 font-serif">180+</dt>
                    <dd class="text-sm text-slate-500 mt-1">Faculty Members</dd>
                </div>
                <div>
                    <dt class="text-3xl font-bold text-school-800 font-serif">98%</dt>
                    <dd class="text-sm text-slate-500 mt-1">Uptime Reliability</dd>
                </div>
                <div>
                    <dt class="text-3xl font-bold text-school-800 font-serif">50+</dt>
                    <dd class="text-sm text-slate-500 mt-1">Institutions Trust Us</dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- ===================== FEATURES ===================== --}}
    <section id="features" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900 font-serif mb-4">Built for every role in your school</h2>
                <p class="text-slate-500 max-w-xl mx-auto">Admins, faculty, and students each get a tailored experience — with the right tools, at the right time.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">

                {{-- Feature 1 --}}
                <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-school-50 flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-school-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Enrollment Management</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Process student applications and enrollments with a clear, step-by-step workflow for every academic term.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Smart Scheduling</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Build conflict-free class schedules automatically, with real-time detection of room and faculty collisions.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Grades & Attendance</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Faculty can record grades and attendance seamlessly, while students and parents get real-time visibility.</p>
                </div>

                {{-- Feature 4 --}}
                <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Role-Based Access</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Admins, faculty, and students each see only what they need — with fine-grained permissions across every module.</p>
                </div>

                {{-- Feature 5 --}}
                <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Assignments & Submissions</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Create and manage assignments with a Google Forms-style submission system students can use from any device.</p>
                </div>

                {{-- Feature 6 --}}
                <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Reports & Analytics</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Get insights on enrollment trends, student performance, and attendance at a glance through clean dashboards.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- ===================== CTA ===================== --}}
    <section class="bg-school-800 py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-3xl font-bold text-white font-serif mb-4">Ready to modernize your school?</h2>
            <p class="text-slate-300 mb-8">Join institutions that have already simplified their operations with SchoolSys.</p>
            @if (Route::has('register'))
            <a href="{{ route('register') }}"
                class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white text-school-800 font-semibold text-sm hover:bg-slate-100 transition-colors">
                Create your account
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            @endif
        </div>
    </section>

</x-guest-layout>