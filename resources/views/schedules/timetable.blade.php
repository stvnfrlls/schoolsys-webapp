<x-app-layout>
    @section('page-title', 'Schedule Timetable')
    @section('breadcrumb', 'Weekly timetable view by section')

    {{-- Filters form (hidden, submits on change) --}}
    <form method="GET" id="filter-form">

        {{-- Page card --}}
        <div class="bg-white rounded-xl border border-slate-200">

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                    {{-- Title --}}
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">Schedule Timetable</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Weekly view — select a section and school year</p>
                    </div>

                    {{-- Controls --}}
                    <div class="flex flex-wrap items-center gap-2">

                        {{-- School Year --}}
                        <select
                            name="school_year_id"
                            onchange="document.getElementById('filter-form').submit()"
                            class="min-w-[9rem] text-sm text-slate-700 border border-slate-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-school-300 focus:border-school-400 transition-colors">
                            @foreach ($schoolYears as $sy)
                                <option value="{{ $sy->id }}" @selected($sy->id == $selectedYear)>
                                    {{ $sy->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Section --}}
                        <select
                            name="section_id"
                            onchange="document.getElementById('filter-form').submit()"
                            class="text-sm text-slate-700 border border-slate-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-school-300 focus:border-school-400 transition-colors">
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" @selected($section->id == $selectedSection)>
                                    {{ $section->gradeLevel->name }} — {{ $section->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Divider --}}
                        <div class="hidden sm:block h-6 w-px bg-slate-200"></div>

                        {{-- List View toggle --}}
                        <a href="{{ route('schedules.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            List View
                        </a>

                        {{-- New Schedule --}}
                        @can('create schedules')
                            <a href="{{ route('schedules.create') }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                New Schedule
                            </a>
                        @endcan

                    </div>
                </div>
            </div>

            {{-- Timetable body --}}
            <div class="p-6">

                @php
                    $days = [
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                    ];

                    // Map subject names → Tailwind color classes (bg + text + border-left accent)
                    $subjectStyles = [
                        'Mathematics'        => ['bg' => 'bg-violet-50',  'text' => 'text-violet-800',  'sub' => 'text-violet-500',  'border' => 'border-l-violet-400'],
                        'English'            => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-800', 'sub' => 'text-emerald-500', 'border' => 'border-l-emerald-400'],
                        'Science'            => ['bg' => 'bg-sky-50',     'text' => 'text-sky-800',     'sub' => 'text-sky-500',     'border' => 'border-l-sky-400'],
                        'Filipino'           => ['bg' => 'bg-orange-50',  'text' => 'text-orange-800',  'sub' => 'text-orange-500',  'border' => 'border-l-orange-400'],
                        'MAPEH'              => ['bg' => 'bg-amber-50',   'text' => 'text-amber-800',   'sub' => 'text-amber-500',   'border' => 'border-l-amber-400'],
                        'Araling Panlipunan' => ['bg' => 'bg-pink-50',    'text' => 'text-pink-800',    'sub' => 'text-pink-500',    'border' => 'border-l-pink-400'],
                        'TLE'                => ['bg' => 'bg-teal-50',    'text' => 'text-teal-800',    'sub' => 'text-teal-500',    'border' => 'border-l-teal-400'],
                        'EPP'                => ['bg' => 'bg-lime-50',    'text' => 'text-lime-800',    'sub' => 'text-lime-500',    'border' => 'border-l-lime-400'],
                        'ESP'                => ['bg' => 'bg-rose-50',    'text' => 'text-rose-800',    'sub' => 'text-rose-500',    'border' => 'border-l-rose-400'],
                    ];

                    $fallbackStyle = ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'sub' => 'text-slate-400', 'border' => 'border-l-slate-300'];
                @endphp

                @if ($timeSlots->isEmpty())

                    {{-- Empty state --}}
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-700 mb-1">No schedules found</p>
                        <p class="text-xs text-slate-400">No schedules have been set for this section and school year yet.</p>
                        @can('create schedules')
                            <a href="{{ route('schedules.create') }}"
                                class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                                + New Schedule
                            </a>
                        @endcan
                    </div>

                @else

                    {{-- Subject legend --}}
                    @php
                        $usedSubjects = $timeSlots
                            ->flatMap(fn($slot) => collect($days)->keys()
                                ->map(fn($dayNum) => collect($timetable[$dayNum] ?? [])
                                    ->first(fn($s) => $s->time_start == $slot['start'])?->subject?->name
                                )
                            )
                            ->filter()
                            ->unique()
                            ->values();
                    @endphp

                    @if ($usedSubjects->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-3 mb-5">
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">Subjects</p>
                            @foreach ($usedSubjects as $subjectName)
                                @php $style = $subjectStyles[$subjectName] ?? $fallbackStyle; @endphp
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $style['bg'] }} {{ $style['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ str_replace('text-', 'bg-', $style['sub']) }}"></span>
                                    {{ $subjectName }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Timetable grid --}}
                    <div class="overflow-x-auto -mx-6 px-6">
                        <table class="w-full text-left border-separate border-spacing-0 min-w-[600px]">

                            {{-- Header row --}}
                            <thead>
                                <tr>
                                    <th class="w-28 pb-3 pr-4 text-xs font-semibold text-slate-400 uppercase tracking-widest">
                                        Time
                                    </th>
                                    @foreach ($days as $dayNum => $dayName)
                                        <th class="pb-3 px-2 text-xs font-semibold text-slate-400 uppercase tracking-widest text-center">
                                            {{ $dayName }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @foreach ($timeSlots as $slot)
                                    <tr class="group">

                                        {{-- Time label --}}
                                        <td class="py-3 pr-4 align-top">
                                            <p class="text-xs font-semibold text-slate-600 whitespace-nowrap">
                                                {{ date('h:i A', strtotime($slot['start'])) }}
                                            </p>
                                            <p class="text-xs text-slate-400 whitespace-nowrap">
                                                {{ date('h:i A', strtotime($slot['end'])) }}
                                            </p>
                                        </td>

                                        {{-- Day cells --}}
                                        @foreach ($days as $dayNum => $dayName)
                                            @php
                                                $match = collect($timetable[$dayNum] ?? [])
                                                    ->first(fn($s) => $s->time_start == $slot['start']);
                                                $style = $match
                                                    ? ($subjectStyles[$match->subject?->name] ?? $fallbackStyle)
                                                    : null;
                                            @endphp
                                            <td class="py-2 px-2 align-top">
                                                @if ($match)
                                                    <a href="{{ route('schedules.show', $match) }}"
                                                        class="group/cell flex flex-col gap-0.5 {{ $style['bg'] }} border-l-2 {{ $style['border'] }} rounded-lg px-3 py-2.5 h-full transition-opacity hover:opacity-80">
                                                        <span class="text-xs font-semibold {{ $style['text'] }} leading-tight">
                                                            {{ $match->subject?->name ?? '—' }}
                                                        </span>
                                                        <span class="text-xs {{ $style['sub'] }} leading-tight truncate">
                                                            @if ($match->faculty)
                                                                {{ $match->faculty->last_name }}, {{ $match->faculty->first_name }}
                                                            @else
                                                                —
                                                            @endif
                                                        </span>
                                                        @if ($match->room)
                                                            <span class="text-xs {{ $style['sub'] }} leading-tight opacity-75">
                                                                {{ $match->room }}
                                                            </span>
                                                        @endif
                                                    </a>
                                                @else
                                                    <div class="h-full min-h-[3.5rem] rounded-lg border border-dashed border-slate-200 bg-slate-50/50"></div>
                                                @endif
                                            </td>
                                        @endforeach

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    {{-- Row count note --}}
                    <p class="mt-4 text-xs text-slate-400">
                        Showing {{ $timeSlots->count() }} time slot{{ $timeSlots->count() !== 1 ? 's' : '' }}
                        across {{ collect($days)->count() }} days
                        for the selected section.
                    </p>

                @endif
            </div>

        </div>
    </form>

</x-app-layout>