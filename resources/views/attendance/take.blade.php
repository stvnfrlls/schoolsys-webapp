<x-app-layout>
    @section('page-title', 'Take Attendance')
    @section('breadcrumb', 'Select a schedule and mark student attendance')

    {{-- Flash banners --}}
    @if (session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Step 1 — Schedule & Date Picker --}}
    <div class="bg-white rounded-xl border border-slate-200 mb-5">

        <div class="flex flex-col gap-4 px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Select Schedule & Date</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Choose a schedule and date to load the student list</p>
                </div>
                <a href="{{ route('attendance.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 text-sm font-medium rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                    <span class="max-sm:hidden">Back to Records</span>
                    <span class="sm:hidden">Back</span>
                </a>
            </div>
        </div>

        <div class="px-4 sm:px-6 py-4 sm:py-5">
            <form method="GET" action="{{ route('attendance.take') }}"
                class="flex flex-wrap items-end gap-3">

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        School Year
                    </label>
                    <select name="school_year_id" id="sy-select"
                        class="py-2 pl-3 pr-8 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 appearance-none bg-white text-slate-700 min-w-[140px]">
                        <option value="">— Select —</option>
                        @foreach ($schoolYears as $sy)
                            <option value="{{ $sy->id }}"
                                {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>
                                {{ $sy->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Schedule
                    </label>
                    <select name="schedule_id" id="schedule-select"
                        class="py-2 pl-3 pr-8 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 appearance-none bg-white text-slate-700 min-w-[240px] disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ request('school_year_id') ? '' : 'disabled' }}>
                        <option value="">— Select school year first —</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Date
                    </label>
                    <input type="date" name="date" value="{{ $date }}"
                        class="py-2 px-3 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 text-slate-700">
                </div>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    Load Students
                </button>

            </form>
        </div>
    </div>

    {{-- Step 2 — Attendance Sheet --}}
    @if ($schedule && $students->isNotEmpty())

        <form method="POST" action="{{ route('attendance.store') }}" id="attendance-form">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="bg-white rounded-xl border border-slate-200">

                {{-- Sheet header --}}
                <div class="flex flex-col gap-4 px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100">

                    {{-- Schedule info + save button --}}
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-800 font-sans">
                                {{ $schedule->subject->name ?? '—' }}
                                <span class="text-slate-300 mx-1">·</span>
                                {{ $schedule->section->gradeLevel->name ?? '' }}
                                {{ $schedule->section->name ?? '—' }}
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                @php $days = ['','Monday','Tuesday','Wednesday','Thursday','Friday']; @endphp
                                {{ $days[$schedule->day_of_week] ?? '—' }}
                                &nbsp;·&nbsp;
                                {{ \Carbon\Carbon::parse($schedule->time_start)->format('g:i A') }}
                                –
                                {{ \Carbon\Carbon::parse($schedule->time_end)->format('g:i A') }}
                                &nbsp;·&nbsp;
                                {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($existing->isNotEmpty())
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-xs font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                    Overwrites existing
                                </span>
                            @endif
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span class="max-sm:hidden">Save Attendance</span>
                                <span class="sm:hidden">Save</span>
                            </button>
                        </div>
                    </div>

                    {{-- Quick-mark toolbar + counts --}}
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Mark all:</span>
                            @foreach ([
                                'present' => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'hover:bg-emerald-100'],
                                'late'    => ['bg-amber-50',   'text-amber-700',   'border-amber-200',   'hover:bg-amber-100'],
                                'absent'  => ['bg-red-50',     'text-red-700',     'border-red-200',     'hover:bg-red-100'],
                                'excused' => ['bg-blue-50',    'text-blue-700',    'border-blue-200',    'hover:bg-blue-100'],
                            ] as $s => [$bg, $text, $border, $hover])
                                <button type="button" onclick="markAll('{{ $s }}')"
                                    class="inline-flex items-center px-3 py-1 rounded-lg border {{ $border }} {{ $bg }} {{ $text }} {{ $hover }} text-xs font-medium transition-colors">
                                    {{ ucfirst($s) }}
                                </button>
                            @endforeach
                        </div>

                        <span class="text-xs text-slate-400">
                            <span id="student-count" class="font-semibold text-slate-600">{{ $students->count() }}</span>
                            students
                        </span>
                    </div>
                </div>

                {{-- Student table --}}
                <div class="overflow-x-auto px-4 sm:px-6 py-4">
                    <table class="w-full text-sm attendance-take-dt">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Student</th>
                                <th class="text-center">Student No.</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $i => $student)
                                @php $rec = $existing->get($student->id); @endphp
                                <tr>
                                    <td class="text-slate-400 text-xs w-8">{{ $i + 1 }}</td>

                                    <td>
                                        <div class="font-medium text-slate-800">
                                            {{ $student->last_name }}, {{ $student->first_name }}
                                            {{ $student->middle_name ? mb_substr($student->middle_name, 0, 1) . '.' : '' }}
                                        </div>
                                    </td>

                                    <td class="font-mono text-xs text-slate-400 text-center">
                                        {{ $student->student_number }}
                                    </td>

                                    <td>
                                        <div class="flex gap-1 flex-wrap justify-center">
                                            @foreach ([
                                                'present' => ['peer-checked:bg-emerald-100', 'peer-checked:text-emerald-800', 'peer-checked:border-emerald-300'],
                                                'late'    => ['peer-checked:bg-amber-100',   'peer-checked:text-amber-800',   'peer-checked:border-amber-300'],
                                                'absent'  => ['peer-checked:bg-red-100',     'peer-checked:text-red-800',     'peer-checked:border-red-300'],
                                                'excused' => ['peer-checked:bg-blue-100',    'peer-checked:text-blue-800',    'peer-checked:border-blue-300'],
                                            ] as $s => [$pBg, $pText, $pBorder])
                                                <label class="cursor-pointer">
                                                    <input type="radio"
                                                        name="records[{{ $student->id }}]"
                                                        value="{{ $s }}"
                                                        class="sr-only peer"
                                                        {{ ($rec?->status ?? 'absent') === $s ? 'checked' : '' }}>
                                                    <span class="inline-block rounded-lg px-2.5 py-1 text-xs border
                                                        border-slate-200 text-slate-400 hover:bg-slate-50
                                                        {{ $pBg }} {{ $pText }} {{ $pBorder }}
                                                        peer-checked:font-semibold transition-colors">
                                                        {{ ucfirst($s) }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td>
                                        <input type="text"
                                            name="remarks[{{ $student->id }}]"
                                            value="{{ old("remarks.{$student->id}", $rec?->remarks) }}"
                                            placeholder="Optional"
                                            class="w-full min-w-[140px] py-1.5 px-2.5 text-xs border border-slate-200 rounded-lg
                                                   focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600
                                                   text-slate-700 placeholder-slate-300">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Sheet footer — repeat save --}}
                <div class="flex items-center justify-end gap-3 px-4 sm:px-6 py-4 border-t border-slate-100">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Save Attendance
                    </button>
                </div>

            </div>
        </form>

    @elseif (request('schedule_id') && $students->isEmpty())

        <div class="bg-white rounded-xl border border-slate-200 px-6 py-16 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
            <p class="text-sm font-medium text-slate-500">No enrolled students found for this schedule.</p>
            <p class="text-xs text-slate-400 mt-1">Make sure students are enrolled for the selected school year.</p>
        </div>

    @elseif (! request('schedule_id'))

        <div class="bg-white rounded-xl border border-slate-200 px-6 py-20 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-slate-400" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-600">Select a schedule and date above to begin.</p>
            <p class="text-xs text-slate-400 mt-2 max-w-xs mx-auto leading-relaxed">
                Choose a school year and schedule, then click <span class="font-medium text-slate-500">Load Students</span> to pull up the attendance sheet.
            </p>
        </div>

    @endif

    @push('styles')
        <style>
            /* ── Attendance sheet table ── */
            table.attendance-take-dt {
                border-collapse: collapse;
                width: 100%;
            }

            table.attendance-take-dt thead tr {
                background-color: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            table.attendance-take-dt thead th {
                font-size: 0.7rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #64748b;
                padding: 0.65rem 0.75rem;
                white-space: nowrap;
            }

            table.attendance-take-dt tbody tr {
                border-bottom: 1px solid #f1f5f9;
                transition: background-color 0.1s;
            }

            table.attendance-take-dt tbody tr:hover {
                background-color: #f8fafc;
            }

            table.attendance-take-dt tbody tr:last-child {
                border-bottom: none;
            }

            table.attendance-take-dt tbody td {
                padding: 0.75rem;
                vertical-align: middle;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script>
            const sySelect       = document.getElementById('sy-select');
            const scheduleSelect = document.getElementById('schedule-select');
            const dateInput      = document.querySelector('input[name="date"]');
            const oldScheduleId  = "{{ request('schedule_id') }}";

            if (sySelect) {
                sySelect.addEventListener('change', loadSchedules);

                // Re-load when date changes (different day = different schedules)
                dateInput?.addEventListener('change', () => {
                    if (sySelect.value) loadSchedules();
                });

                // Trigger on page load if school year already selected
                if (sySelect.value) {
                    loadSchedules();
                }
            }

            function markAll(status) {
                document.querySelectorAll(`input[type="radio"][value="${status}"]`)
                    .forEach(r => { r.checked = true; });
            }

            function loadSchedules() {
                const yearId = sySelect.value;
                const date   = dateInput?.value ?? '';

                scheduleSelect.innerHTML = '<option value="">Loading…</option>';
                scheduleSelect.disabled  = true;

                if (!yearId) {
                    scheduleSelect.innerHTML = '<option value="">— Select school year first —</option>';
                    return;
                }

                fetch(`{{ route('attendance.load-schedules') }}?school_year_id=${yearId}&date=${date}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(r => r.json())
                .then(data => {
                    scheduleSelect.innerHTML = '<option value="">— Select a schedule —</option>';

                    if (data.length === 0) {
                        scheduleSelect.innerHTML = '<option value="">No schedules for this day</option>';
                        return;
                    }

                    data.forEach(s => {
                        // Day name no longer needed in label — already filtered to one day
                        const label = `${s.subject_name} — ${s.section_name} — ${s.time_start}`;
                        const opt   = new Option(label, s.id, false, String(s.id) === oldScheduleId);
                        scheduleSelect.appendChild(opt);
                    });

                    scheduleSelect.disabled = false;
                })
                .catch(() => {
                    scheduleSelect.innerHTML = '<option value="">Failed to load schedules.</option>';
                    scheduleSelect.disabled  = false;
                });
            }
        </script>
    @endpush

</x-app-layout>