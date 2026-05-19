<x-app-layout>
    @section('page-title', 'Attendance')
    @section('breadcrumb', 'View and manage student attendance records')

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

    <div class="bg-white rounded-xl border border-slate-200">

        {{-- Card header --}}
        <div class="flex flex-col gap-4 px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100">

            {{-- Title row --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">All Records</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Attendance records across all schedules</p>
                </div>
                <div class="flex items-center gap-2">
                    @can('view attendance summary')
                        <a href="{{ route('attendance.summary') }}"
                            class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 text-sm font-medium rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            <span class="max-sm:hidden">Summary</span>
                        </a>
                    @endcan
                    @can('create attendance')
                        <a href="{{ route('attendance.take') }}"
                            class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span class="max-sm:hidden">Take Attendance</span>
                            <span class="sm:hidden">Take</span>
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Filters + Search + Export row --}}
            <div id="search-export-row" class="flex flex-col gap-2 md:flex-row md:items-center md:flex-wrap justify-between">

                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-2">

                    <select id="filter-section"
                        class="py-2 pl-3 pr-8 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 appearance-none bg-white text-slate-600">
                        <option value="">All Sections</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}">
                                {{ $section->gradeLevel->name ?? '' }} — {{ $section->name }}
                            </option>
                        @endforeach
                    </select>

                    <select id="filter-status"
                        class="py-2 pl-3 pr-8 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 appearance-none bg-white text-slate-600">
                        <option value="">All Statuses</option>
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="absent">Absent</option>
                        <option value="excused">Excused</option>
                    </select>

                    <input type="date" id="filter-date"
                        class="py-2 px-3 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 text-slate-600">

                    <button onclick="applyFilters()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Filter
                    </button>

                    <button onclick="clearFilters()"
                        class="text-sm text-slate-400 hover:text-slate-600 transition-colors px-1">
                        Clear
                    </button>
                </div>

                {{-- Search + Export --}}
                <div class="flex items-center gap-2 md:ml-auto">
                    <div class="relative flex-1 md:flex-none">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input id="attendance-search" type="text" placeholder="Search..."
                            class="pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 w-full md:w-36 lg:w-56">
                    </div>

                    <x-dt-export-buttons />
                </div>
            </div>
        </div>

        {{-- DataTable --}}
        <div class="overflow-x-auto px-4 sm:px-6 py-4">
            {{ $dataTable->table(['class' => 'w-full text-sm attendance-dt text-center']) }}
        </div>

    </div>

    {{-- Delete confirmation modal --}}
    <div id="delete-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <h3 class="text-base font-semibold text-slate-800 mb-2">Delete this record?</h3>
            <p class="text-sm text-slate-500 mb-6">
                This attendance entry will be permanently removed and cannot be undone.
            </p>
            <div class="flex justify-end gap-3">
                <button onclick="document.getElementById('delete-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">
                        Yes, delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

        <style>
            /* ── Hide DataTables default UI ── */
            #attendance-table_filter,
            .dt-buttons {
                display: none !important;
            }

            #attendance-table_length {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.75rem;
                color: #64748b;
                white-space: nowrap;
            }

            #attendance-table_length label {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.75rem;
                color: #64748b;
                white-space: nowrap;
            }

            #attendance-table_length select {
                appearance: none;
                -webkit-appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.5rem center;
                padding: 0.3rem 1.75rem 0.3rem 0.6rem;
                font-size: 0.75rem;
                font-weight: 500;
                color: #475569;
                background-color: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                cursor: pointer;
                transition: border-color 0.15s, box-shadow 0.15s;
                min-width: 3.5rem;
            }

            #attendance-table_length select:focus {
                outline: none;
                border-color: #1e3a8a;
                box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.15);
            }

            /* ── Table base ── */
            table.attendance-dt {
                border-collapse: collapse;
                width: 100% !important;
            }

            table.attendance-dt thead tr {
                background-color: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            table.attendance-dt thead th,
            table.attendance-dt td {
                font-size: 0.7rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #64748b;
                padding: 0.75rem;
                border-bottom: none !important;
                white-space: nowrap;
                text-align: center;
            }

            table.attendance-dt tbody tr {
                border-bottom: 1px solid #f1f5f9;
                transition: background-color 0.1s;
                cursor: pointer;
            }

            table.attendance-dt tbody tr:hover {
                background-color: #f8fafc !important;
            }

            table.attendance-dt tbody tr:last-child {
                border-bottom: none;
            }

            table.attendance-dt tbody td {
                padding: 0.875rem 0.75rem;
                color: #475569;
                font-size: 0.875rem;
                vertical-align: middle;
            }

            /* ── Child row (responsive collapse) ── */
            table.attendance-dt tbody tr.child td {
                padding: 0.5rem 1rem !important;
                background: #f8fafc;
                text-align: left !important;
            }

            table.attendance-dt tbody tr.child ul.dtr-details {
                display: block;
                width: 100%;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            table.attendance-dt tbody tr.child ul.dtr-details li {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.375rem 0;
                border-bottom: 1px solid #f1f5f9;
                font-size: 0.8rem;
            }

            table.attendance-dt tbody tr.child ul.dtr-details li:last-child {
                border-bottom: none;
            }

            table.attendance-dt tbody tr.child ul.dtr-details li span.dtr-title {
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                font-size: 0.7rem;
            }

            table.attendance-dt tbody tr.child ul.dtr-details li span.dtr-data {
                color: #475569;
            }

            /* ── Pagination ── */
            #attendance-table_paginate {
                padding: 0.75rem 1rem;
                border-top: 1px solid #f1f5f9;
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            #attendance-table_paginate .paginate_button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 2rem;
                height: 2rem;
                padding: 0 0.5rem;
                border-radius: 0.5rem;
                font-size: 0.8rem;
                color: #64748b !important;
                cursor: pointer;
                transition: all 0.15s;
            }

            #attendance-table_paginate .paginate_button:hover {
                background-color: #f1f5f9 !important;
                color: #1e40af !important;
            }

            #attendance-table_paginate .paginate_button.current {
                background-color: #1e3a8a !important;
                color: #fff !important;
                font-weight: 600;
            }

            #attendance-table_paginate .paginate_button.disabled {
                color: #cbd5e1 !important;
                cursor: not-allowed;
            }

            /* ── Info text ── */
            #attendance-table_info {
                padding: 0.75rem 1rem;
                font-size: 0.75rem;
                color: #94a3b8;
                border-top: 1px solid #f1f5f9;
                text-align: center;
            }

            table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control,
            table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control {
                text-align: justify;
            }

            @media (max-width: 639px) {
                #attendance-table_length {
                    justify-content: center;
                }
            }

            @media (min-width: 640px) {
                #attendance-table_paginate { justify-content: flex-end; }
                #attendance-table_info     { text-align: left; }

                table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control,
                table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control {
                    text-align: unset;
                }
            }

            @media (min-width: 768px) {
                #attendance-table_length { justify-content: flex-start; }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

        <script>
            function waitForTable(id, callback) {
                if (window.LaravelDataTables && window.LaravelDataTables[id]) {
                    callback(window.LaravelDataTables[id]);
                } else {
                    setTimeout(() => waitForTable(id, callback), 50);
                }
            }

            waitForTable('attendance-table', function (table) {
                // Move length control into the header row
                const $length = $('#attendance-table_length').detach();
                $('#search-export-row').prepend($length);

                // Wire custom search input
                document.getElementById('attendance-search').addEventListener('input', function () {
                    table.search(this.value).draw();
                });

                // Wire export buttons via dt-export-buttons component
                const buttons = new $.fn.dataTable.Buttons(table, {
                    buttons: [
                        { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
                        { extend: 'pdf',   exportOptions: { columns: ':not(:last-child)' } },
                    ]
                });

                const $btns = buttons.container().find('button');
                document.querySelector('[data-dt-button="excel"]')
                    .addEventListener('click', () => $btns.eq(0).click());
                document.querySelector('[data-dt-button="pdf"]')
                    .addEventListener('click', () => $btns.eq(1).click());
            });

            // Filter controls — reload with extra params (picked up by AttendanceDataTable::query)
            function applyFilters() {
                if (window.LaravelDataTables?.['attendance-table']) {
                    window.LaravelDataTables['attendance-table'].ajax.reload();
                }
            }

            function clearFilters() {
                document.getElementById('filter-section').value = '';
                document.getElementById('filter-status').value  = '';
                document.getElementById('filter-date').value    = '';
                if (window.LaravelDataTables?.['attendance-table']) {
                    window.LaravelDataTables['attendance-table'].ajax.reload();
                }
            }

            // Delete modal
            function confirmDelete(id) {
                document.getElementById('delete-form').action = `/attendance/${id}`;
                document.getElementById('delete-modal').classList.remove('hidden');
            }
        </script>
    @endpush

</x-app-layout>