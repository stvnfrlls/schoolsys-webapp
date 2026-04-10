<x-app-layout>
    @section('title', 'Dashboard')
    @section('page-title', 'Dashboard')
    @section('breadcrumb', 'Welcome back, ' . Auth::user()->name)

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-school-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-school-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">2,418</p>
                <p class="text-xs text-slate-500 mt-0.5">Total Students</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">184</p>
                <p class="text-xs text-slate-500 mt-0.5">Faculty Members</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">96.4%</p>
                <p class="text-xs text-slate-500 mt-0.5">Attendance Rate</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">312</p>
                <p class="text-xs text-slate-500 mt-0.5">Active Classes</p>
            </div>
        </div>

    </div>

    {{-- Lower section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Recent activity --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-5">Recent Activity</h3>
            <div class="space-y-4">
                @foreach ([
                ['icon' => 'user', 'text' => 'New student enrolled: Maria Santos — Grade 10', 'time' => '2 min ago', 'color' => 'bg-school-50 text-school-700'],
                ['icon' => 'calendar', 'text' => 'Schedule updated for MATH-101 — Room 205', 'time' => '18 min ago', 'color' => 'bg-sky-50 text-sky-600'],
                ['icon' => 'check', 'text' => 'Grades submitted by Prof. Reyes — CHEM-201', 'time' => '1 hr ago', 'color' => 'bg-emerald-50 text-emerald-600'],
                ['icon' => 'user', 'text' => 'New faculty account created: Dr. Lim', 'time' => '3 hrs ago', 'color' => 'bg-violet-50 text-violet-600'],
                ] as $activity)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg {{ $activity['color'] }} flex items-center justify-center shrink-0 mt-0.5">
                        @if($activity['icon'] === 'user')
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        @elseif($activity['icon'] === 'calendar')
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-700">{{ $activity['text'] }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $activity['time'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-5">Quick Actions</h3>
            <div class="space-y-2">
                <a href="#" class="flex items-center gap-3 w-full p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-school-50 flex items-center justify-center text-school-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span class="text-sm text-slate-700 font-medium group-hover:text-slate-900">Enroll New Student</span>
                </a>
                <a href="#" class="flex items-center gap-3 w-full p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center text-sky-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                        </svg>
                    </div>
                    <span class="text-sm text-slate-700 font-medium group-hover:text-slate-900">Build Schedule</span>
                </a>
                <a href="#" class="flex items-center gap-3 w-full p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375" />
                        </svg>
                    </div>
                    <span class="text-sm text-slate-700 font-medium group-hover:text-slate-900">Submit Grades</span>
                </a>
                <a href="#" class="flex items-center gap-3 w-full p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                        </svg>
                    </div>
                    <span class="text-sm text-slate-700 font-medium group-hover:text-slate-900">View Reports</span>
                </a>
            </div>
        </div>

    </div>

</x-app-layout>