<x-app-layout>
    @section('page-title', 'Attendance Summary')
    @section('breadcrumb', 'Aggregate attendance rates per student')

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

    {{-- At-a-glance stat cards --}}
    @if ($summaries->isNotEmpty())
        @php
            $avgRate      = $summaries->avg('rate');
            $atRiskCount  = $summaries->filter(fn($s) => $s['rate'] < 75)->count();
            $perfectCount = $summaries->filter(fn($s) => $s['rate'] == 100)->count();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

            <div class="bg-white rounded-xl border border-slate-200 px-6 py-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Students</p>
                <p class="mt-2 text-2xl font-bold text-slate-800">{{ $summaries->count() }}</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 px-6 py-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Avg. Rate</p>
                <p class="mt-2 text-2xl font-bold {{ $avgRate >= 75 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ number_format($avgRate, 1) }}%
                </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 px-6 py-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">At Risk <span class="normal-case font-normal text-slate-300">(&lt;75%)</span></p>
                <p class="mt-2 text-2xl font-bold {{ $atRiskCount > 0 ? 'text-red-600' : 'text-slate-800' }}">
                    {{ $atRiskCount }}
                </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 px-6 py-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Perfect</p>
                <p class="mt-2 text-2xl font-bold text-slate-800">{{ $perfectCount }}</p>
            </div>

        </div>
    @endif

    {{-- Main card --}}
    <div class="bg-white rounded-xl border border-slate-200">

        {{-- Card header --}}
        <div class="flex flex-col gap-4 px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100">

            {{-- Title row --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Student Summary</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Attendance breakdown per student across all sessions</p>
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

            {{-- Filters row --}}
            <div class="flex flex-wrap items-end gap-3">
                <form method="GET" action="{{ route('attendance.summary') }}"
                    class="flex flex-wrap items-end gap-3 w-full">

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            School Year
                        </label>
                        <select name="school_year_id"
                            class="py-2 pl-3 pr-8 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 appearance-none bg-white text-slate-700">
                            <option value="">All years</option>
                            @foreach ($schoolYears as $sy)
                                <option value="{{ $sy->id }}"
                                    {{ $schoolYearId == $sy->id ? 'selected' : '' }}>
                                    {{ $sy->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Section
                        </label>
                        <select name="section_id"
                            class="py-2 pl-3 pr-8 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 appearance-none bg-white text-slate-700">
                            <option value="">All sections</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ $sectionId == $section->id ? 'selected' : '' }}>
                                    {{ $section->gradeLevel->name ?? '' }} — {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Filter
                    </button>

                    <a href="{{ route('attendance.summary') }}"
                        class="text-sm text-slate-400 hover:text-slate-600 transition-colors py-2">
                        Clear
                    </a>

                    @if ($summaries->isNotEmpty())
                        <button type="button" onclick="exportCSV()"
                            class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 text-sm font-medium rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors ml-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span class="max-sm:hidden">Export CSV</span>
                            <span class="sm:hidden">CSV</span>
                        </button>
                    @endif

                </form>
            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm summary-dt" id="summary-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-left">Student</th>
                        <th class="text-left">Student No.</th>
                        <th>Present</th>
                        <th>Late</th>
                        <th>Absent</th>
                        <th>Excused</th>
                        <th>Total</th>
                        <th class="text-left">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($summaries as $i => $row)
                        <tr class="{{ $row['rate'] < 75 ? 'at-risk' : '' }}">

                            <td class="text-slate-400 text-xs text-center">{{ $i + 1 }}</td>

                            <td>
                                <div class="font-semibold text-slate-800">
                                    {{ $row['student']->last_name }},
                                    {{ $row['student']->first_name }}
                                    {{ $row['student']->middle_name ? mb_substr($row['student']->middle_name, 0, 1) . '.' : '' }}
                                </div>
                            </td>

                            <td class="font-mono text-xs text-slate-400">
                                {{ $row['student']->student_number }}
                            </td>

                            <td class="text-center font-semibold text-emerald-600">
                                {{ $row['present_count'] }}
                            </td>
                            <td class="text-center font-semibold text-amber-600">
                                {{ $row['late_count'] }}
                            </td>
                            <td class="text-center font-semibold text-red-600">
                                {{ $row['absent_count'] }}
                            </td>
                            <td class="text-center font-semibold text-blue-600">
                                {{ $row['excused_count'] }}
                            </td>
                            <td class="text-center text-slate-500">
                                {{ $row['total'] }}
                            </td>

                            <td class="min-w-[160px]">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-300
                                            {{ $row['rate'] >= 90 ? 'bg-emerald-500' : ($row['rate'] >= 75 ? 'bg-amber-400' : 'bg-red-500') }}"
                                            style="width: {{ $row['rate'] }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs font-semibold w-11 text-right shrink-0
                                        {{ $row['rate'] >= 90 ? 'text-emerald-600' : ($row['rate'] >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                                        {{ $row['rate'] }}%
                                    </span>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="py-20 text-center">
                                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-100 mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-slate-400"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600">
                                        @if (! $schoolYearId)
                                            Select a school year to view the summary.
                                        @else
                                            No attendance records found for the selected filters.
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-400 mt-2 max-w-xs mx-auto leading-relaxed">
                                        @if (! $schoolYearId)
                                            Choose a school year and optionally a section, then click
                                            <span class="font-medium text-slate-500">Filter</span>.
                                        @else
                                            Try adjusting the filters above to find what you're looking for.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Legend --}}
        @if ($summaries->isNotEmpty())
            <div class="flex flex-wrap items-center gap-4 px-4 sm:px-6 py-3 border-t border-slate-100">
                <span class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    ≥ 90% — Good
                </span>
                <span class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                    75–89% — Monitor
                </span>
                <span class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                    &lt;75% — At risk
                </span>
                <span class="ml-auto text-xs text-slate-300">
                    Rate = (present + late) ÷ total
                </span>
            </div>
        @endif

    </div>

    @push('styles')
        <style>
            table.summary-dt {
                border-collapse: collapse;
                width: 100%;
            }

            table.summary-dt thead tr {
                background-color: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            table.summary-dt thead th {
                font-size: 0.7rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #64748b;
                padding: 0.75rem;
                white-space: nowrap;
            }

            table.summary-dt tbody tr {
                border-bottom: 1px solid #f1f5f9;
                transition: background-color 0.1s;
            }

            table.summary-dt tbody tr:hover {
                background-color: #f8fafc;
            }

            table.summary-dt tbody tr.at-risk {
                background-color: rgb(254 242 242 / 0.5);
            }

            table.summary-dt tbody tr.at-risk:hover {
                background-color: rgb(254 242 242 / 0.8);
            }

            table.summary-dt tbody tr:last-child {
                border-bottom: none;
            }

            table.summary-dt tbody td {
                padding: 0.875rem 0.75rem;
                vertical-align: middle;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function exportCSV() {
                const table   = document.getElementById('summary-table');
                const rows    = [...table.querySelectorAll('tr')];
                const headers = [...rows[0].querySelectorAll('th')]
                    .map(th => `"${th.innerText.trim()}"`)
                    .join(',');
                const body = rows.slice(1)
                    .filter(row => row.querySelectorAll('td').length > 1) // skip empty state row
                    .map(row =>
                        [...row.querySelectorAll('td')]
                            .map(td => `"${td.innerText.trim().replace(/"/g, '""')}"`)
                            .join(',')
                    ).join('\n');

                const blob = new Blob([[headers, body].join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a    = Object.assign(document.createElement('a'), {
                    href: URL.createObjectURL(blob),
                    download: `attendance-summary-{{ now()->format('Y-m-d') }}.csv`,
                });
                a.click();
                URL.revokeObjectURL(a.href);
            }
        </script>
    @endpush

</x-app-layout>