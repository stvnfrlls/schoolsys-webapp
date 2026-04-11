<x-app-layout>
    @section('page-title', 'Users')
    @section('breadcrumb', 'Manage system users and their roles')

    {{-- Flash messages --}}
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

    {{-- Page card --}}
    <div class="bg-white rounded-xl border border-slate-200">

        {{-- Card header --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-5 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-semibold text-slate-800 font-sans">All Users</h2>
                <p class="text-xs text-slate-400 mt-0.5">System users with their assigned roles and status</p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">

                {{-- Custom search input (wired to DataTables) --}}
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input
                        id="users-search"
                        type="text"
                        placeholder="Search users..."
                        class="pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 w-full sm:w-56">
                </div>

                {{-- Export buttons injected here by JS --}}
                <div id="dt-buttons" class="flex items-center gap-2">
                    <button type="button" data-dt-button="excel" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-colors whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                        </svg>
                        Excel
                    </button>
                    <button type="button" data-dt-button="pdf" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-colors whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                        </svg>
                        PDF
                    </button>
                </div>

                {{-- Create button --}}
                <a href="{{ route('users.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New User
                </a>


            </div>
        </div>

        {{-- DataTable --}}
        <div class="overflow-x-auto px-6 py-4">
            {{ $dataTable->table(['class' => 'w-full text-sm user-dt text-center']) }}
        </div>

    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <style>
        /* Hide default search box — we use our own */
        #user-table_filter {
            display: none !important;
        }

        /* Table base */
        table.user-dt {
            border-collapse: collapse;
            width: 100% !important;
        }

        /* Header */
        table.user-dt thead tr {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        table.user-dt thead th {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 0.75rem 1rem;
            border-bottom: none !important;
            white-space: nowrap;
        }

        /* Body rows */
        table.user-dt tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.1s;
        }

        table.user-dt tbody tr:hover {
            background-color: #f8fafc !important;
        }

        table.user-dt tbody tr:last-child {
            border-bottom: none;
        }

        table.user-dt tbody td {
            padding: 0.875rem 1rem;
            color: #475569;
            font-size: 0.875rem;
            vertical-align: middle;
        }

        table.dataTable thead th,
        table.dataTable td {
            text-align: center;
        }

        /* Pagination */
        #user-table_paginate {
            padding: 1rem 1.5rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: flex-end;
        }

        #user-table_paginate .paginate_button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            margin: 0 0.1rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            color: #64748b !important;
            cursor: pointer;
            transition: all 0.15s;
        }

        #user-table_paginate .paginate_button:hover {
            background-color: #f1f5f9 !important;
            color: #1e40af !important;
        }

        #user-table_paginate .paginate_button.current {
            background-color: #1e3a8a !important;
            color: #fff !important;
            font-weight: 600;
        }

        #user-table_paginate .paginate_button.disabled {
            color: #cbd5e1 !important;
            cursor: not-allowed;
        }

        /* Length select */
        #user-table_length {
            padding: 0.75rem 1.5rem;
            font-size: 0.8rem;
            color: #64748b;
        }

        #user-table_length select {
            appearance: auto;
            -webkit-appearance: auto;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.2rem 0.4rem;
            font-size: 0.8rem;
            color: #475569;
            margin: 0 0.25rem;
            background-image: none !important;
            background-color: #fff;
            padding-right: 0.4rem !important;
        }

        /* Info text */
        #user-table_info {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }

        /* Processing overlay */
        #user-table_processing {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            box-shadow: none;
            color: #1e3a8a;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Export buttons */
        .dt-buttons .dt-button {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s;
            margin-right: 0.25rem;
        }

        .dt-buttons .dt-button:hover {
            background: #f8fafc;
            color: #1e3a8a;
            border-color: #bfdbfe;
        }

        .dt-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Base button styling — override DataTables defaults */
        .dt-buttons .dt-button.custom-export-btn {
            /* Reset DataTables defaults */
            padding: 0.5rem 1rem !important;
            border: 1px solid var(--color-border-tertiary) !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            background: #1e3a8a !important;
            color: #fff !important;
            cursor: pointer !important;
            transition: all 0.15s !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            white-space: nowrap !important;
            text-decoration: none !important;
            max-height: 36px;
            margin-bottom: 0;
        }

        /* Hover state */
        .dt-buttons .dt-button.custom-export-btn:hover:not(.disabled) {
            background: var(--color-background-secondary) !important;
            color: var(--color-text-primary) !important;
            border-color: var(--color-border-secondary) !important;
        }

        /* Active/pressed state */
        .dt-buttons .dt-button.custom-export-btn:active:not(.disabled) {
            transform: scale(0.98);
            opacity: 0.8;
        }

        /* Disabled state */
        .dt-buttons .dt-button.custom-export-btn.disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }

        /* Icons before button text using ::before pseudo-element */
        .dt-buttons .dt-button.custom-export-btn::before {
            content: '';
            display: inline-block;
            width: 1rem;
            height: 1rem;
            padding: 0.5rem;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Icon content for each button type */
        .dt-buttons .dt-button[data-icon="excel"]::before,
        .dt-buttons .dt-button[data-icon="pdf"]::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2'/%3E%3C/svg%3E");
        }

        /* Hide the DataTables-generated button wrapper */
        .dt-buttons-wrapper {
            display: none !important;
        }

        /* Move buttons to the header container */
        #dt-buttons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    {{-- Yajra-generated init script --}}
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#user-table').on('init.dt', function() {
                const table = $('#user-table').DataTable();

                // Create buttons instance
                const buttons = new $.fn.dataTable.Buttons(table, {
                    buttons: [{
                            extend: 'excel',
                            text: 'Excel',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ]
                });

                // Attach click handlers to pre-rendered buttons
                document.querySelector('[data-dt-button="excel"]').addEventListener('click', function(e) {
                    e.preventDefault();
                    buttons.container().find('button:eq(0)').click();
                });

                document.querySelector('[data-dt-button="pdf"]').addEventListener('click', function(e) {
                    e.preventDefault();
                    buttons.container().find('button:eq(1)').click();
                });
            });
        });
    </script>
    @endpush

</x-app-layout>