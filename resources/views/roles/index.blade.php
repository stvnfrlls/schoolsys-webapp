<x-app-layout>
    @section('page-title', 'Roles')
    @section('breadcrumb', 'Manage system roles and their permissions')

    @if (session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
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
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">All Roles</h2>
                    <p class="text-xs text-slate-400 mt-0.5">System roles with their assigned permissions</p>
                </div>

                <a href="{{ route('roles.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="hidden sm:inline">New Role</span>
                    <span class="sm:hidden">New</span>
                </a>
            </div>

            {{-- Search + Export row --}}
            <div class="flex items-center gap-2 sm:justify-end">

                <div class="relative flex-1 max-w-xs sm:max-w-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input
                        id="roles-search"
                        type="text"
                        placeholder="Search roles..."
                        class="pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 w-full">
                </div>

                <div id="dt-buttons" class="flex items-center gap-2 shrink-0">
                    <button type="button" data-dt-button="excel"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-colors whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                        </svg>
                        <span class="hidden sm:inline">Excel</span>
                    </button>
                    <button type="button" data-dt-button="pdf"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-colors whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                        </svg>
                        <span class="hidden sm:inline">PDF</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- DataTable --}}
        <div class="overflow-x-auto px-4 sm:px-6 py-4">
            {{ $dataTable->table(['class' => 'w-full text-sm roles-dt text-center']) }}
        </div>

    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <style>
        /* ── Hide DataTables default UI ── */
        #roles-table_filter,
        #roles-table_length,
        .dt-buttons {
            display: none !important;
        }

        /* ── Table base ── */
        table.roles-dt {
            border-collapse: collapse;
            width: 100% !important;
        }

        table.roles-dt thead tr {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        table.roles-dt thead th,
        table.dataTable td {
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

        table.roles-dt tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.1s;
            cursor: pointer;
        }

        table.roles-dt tbody tr:hover {
            background-color: #f8fafc !important;
        }

        table.roles-dt tbody tr:last-child {
            border-bottom: none;
        }

        table.roles-dt tbody td {
            padding: 0.875rem 0.75rem;
            color: #475569;
            font-size: 0.875rem;
            vertical-align: middle;
        }

        /* ── Child row ── */
        table.roles-dt tbody tr.child td {
            padding: 0.5rem 1rem !important;
            background: #f8fafc;
            text-align: left !important;
        }

        table.roles-dt tbody tr.child ul.dtr-details {
            display: block;
            width: 100%;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        table.roles-dt tbody tr.child ul.dtr-details li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.375rem 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.8rem;
        }

        table.roles-dt tbody tr.child ul.dtr-details li:last-child {
            border-bottom: none;
        }

        table.roles-dt tbody tr.child ul.dtr-details li span.dtr-title {
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 0.7rem;
        }

        table.roles-dt tbody tr.child ul.dtr-details li span.dtr-data {
            color: #475569;
        }

        /* ── Pagination ── */
        #roles-table_paginate {
            padding: 0.75rem 1rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        @media (min-width: 640px) {
            #roles-table_paginate {
                justify-content: flex-end;
            }
        }

        #roles-table_paginate .paginate_button {
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

        #roles-table_paginate .paginate_button:hover {
            background-color: #f1f5f9 !important;
            color: #1e40af !important;
        }

        #roles-table_paginate .paginate_button.current {
            background-color: #1e3a8a !important;
            color: #fff !important;
            font-weight: 600;
        }

        #roles-table_paginate .paginate_button.disabled {
            color: #cbd5e1 !important;
            cursor: not-allowed;
        }

        /* ── Info text ── */
        #roles-table_info {
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
            text-align: justify;
        }

        @media (min-width: 640px) {
            #roles-table_info {
                text-align: left;
            }

            table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control,
            table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
                text-align: unset;
            }
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
        document.addEventListener('DOMContentLoaded', function() {
            $('#roles-table').on('init.dt', function() {
                const table = $('#roles-table').DataTable();

                // Custom search
                document.getElementById('roles-search').addEventListener('input', function() {
                    table.search(this.value).draw();
                });

                // Export buttons
                const buttons = new $.fn.dataTable.Buttons(table, {
                    buttons: [{
                            extend: 'excel',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                    ]
                });

                const $btns = buttons.container().find('button');
                document.querySelector('[data-dt-button="excel"]').addEventListener('click', () => $btns.eq(0).click());
                document.querySelector('[data-dt-button="pdf"]').addEventListener('click', () => $btns.eq(1).click());
            });
        });
    </script>
    @endpush

</x-app-layout>