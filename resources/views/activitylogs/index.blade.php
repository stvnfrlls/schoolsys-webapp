<x-app-layout>
    @section('page-title', 'Activity Logs')
    @section('breadcrumb', 'System activity and audit trail')

    {{-- Page card --}}
    <div class="bg-white rounded-xl border border-slate-200">

        {{-- Card header --}}
        <div class="flex flex-col gap-4 px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100">

            {{-- Title row --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">System logs</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Track and monitor all system activity</p>
                </div>
            </div>

            {{-- Search + Export row --}}
            <div id="search-export-row" class="flex flex-col gap-2 md:flex-row md:items-center justify-between">
                <div class="flex items-center gap-2 md:ml-auto">

                    <div class="relative flex-1 md:flex-none">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input id="log-search" type="text" placeholder="Search activity..."
                            class="pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 w-full md:w-36 lg:w-56">
                    </div>

                    <x-dt-export-buttons />
                </div>
            </div>
        </div>

        {{-- DataTable --}}
        <div class="overflow-x-auto px-4 sm:px-6 py-4">
            {{ $dataTable->table(['class' => 'w-full text-sm activitylogs-dt text-center']) }}
        </div>

    </div>

    {{-- Detail modal --}}
    <div id="logModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl border border-slate-100 shadow-lg w-full max-w-2xl mx-4">

            {{-- Modal header --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 font-sans">Activity details</h3>
                    <p id="logMeta" class="text-xs text-slate-400 mt-0.5"></p>
                </div>
                <button onclick="closeModal()"
                    class="text-slate-400 hover:text-slate-600 text-lg leading-none px-1">✕</button>
            </div>

            {{-- Modal body --}}
            <div id="logContent" class="overflow-auto max-h-[420px] px-6 py-4"></div>

            {{-- Modal footer --}}
            <div class="flex justify-end px-6 py-3 border-t border-slate-100">
                <button onclick="closeModal()"
                    class="text-xs px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                    Close
                </button>
            </div>

        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

        <style>
            /* Hide default DataTables UI */
            #activitylogs-table_filter,
            .dt-buttons {
                display: none !important;
            }

            #activitylogs-table_length {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.75rem;
                color: #64748b;
                white-space: nowrap;
            }

            #activitylogs-table_length label {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.75rem;
                color: #64748b;
                white-space: nowrap;
            }

            #activitylogs-table_length select {
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

            #activitylogs-table_length select:focus {
                outline: none;
                border-color: #1e3a8a;
                box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.15);
            }

            /* Table base */
            table.activitylogs-dt {
                border-collapse: collapse;
                width: 100% !important;
            }

            /* Header */
            table.activitylogs-dt thead tr {
                background-color: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            table.activitylogs-dt thead th,
            table.activitylogs-dt td {
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

            /* Body rows */
            table.activitylogs-dt tbody tr {
                border-bottom: 1px solid #f1f5f9;
                transition: background-color 0.1s;
            }

            table.activitylogs-dt tbody tr:hover {
                background-color: #f8fafc !important;
            }

            table.activitylogs-dt tbody tr:last-child {
                border-bottom: none;
            }

            table.activitylogs-dt tbody td {
                padding: 0.875rem 0.75rem;
                color: #475569;
                font-size: 0.875rem;
                vertical-align: middle;
            }

            /* Child row (responsive collapsed columns) */
            table.activitylogs-dt tbody tr.child td {
                padding: 0.5rem 1rem !important;
                background: #f8fafc;
                text-align: left !important;
            }

            table.activitylogs-dt tbody tr.child ul.dtr-details {
                display: block;
                width: 100%;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            table.activitylogs-dt tbody tr.child ul.dtr-details li {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.375rem 0;
                border-bottom: 1px solid #f1f5f9;
                font-size: 0.8rem;
            }

            table.activitylogs-dt tbody tr.child ul.dtr-details li:last-child {
                border-bottom: none;
            }

            table.activitylogs-dt tbody tr.child ul.dtr-details li span.dtr-title {
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                font-size: 0.7rem;
            }

            table.activitylogs-dt tbody tr.child ul.dtr-details li span.dtr-data {
                color: #475569;
            }

            /* Pagination */
            #activitylogs-table_paginate {
                padding: 0.75rem 1rem;
                border-top: 1px solid #f1f5f9;
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            @media (max-width: 639px) {
                #activitylogs-table_length {
                    justify-content: center;
                }
            }

            @media (min-width: 640px) {
                #activitylogs-table_paginate {
                    justify-content: flex-end;
                }

                #activitylogs-table_length {
                    justify-content: flex-start;
                }
            }

            @media (max-width: 767px) {
                #activitylogs-table_length {
                    justify-content: flex-start;
                }
            }

            @media (min-width: 768px) {
                #activitylogs-table_length {
                    justify-content: flex-start;
                }
            }

            #activitylogs-table_paginate .paginate_button {
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

            #activitylogs-table_paginate .paginate_button:hover {
                background-color: #f1f5f9 !important;
                color: #1e40af !important;
            }

            #activitylogs-table_paginate .paginate_button.current {
                background-color: #1e3a8a !important;
                color: #fff !important;
                font-weight: 600;
            }

            #activitylogs-table_paginate .paginate_button.disabled {
                color: #cbd5e1 !important;
                cursor: not-allowed;
            }

            /* Info text */
            #activitylogs-table_info {
                padding: 0.75rem 1rem;
                font-size: 0.75rem;
                color: #94a3b8;
                border-top: 1px solid #f1f5f9;
                text-align: center;
            }

            /* Responsive dtr-control alignment fix */
            table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control,
            table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
                text-align: justify;
            }

            @media (min-width: 640px) {
                #activitylogs-table_info {
                    text-align: left;
                }

                table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control,
                table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
                    text-align: unset;
                }
            }

            /* Processing overlay */
            #activitylogs-table_processing {
                background: rgba(255, 255, 255, 0.9);
                border: none;
                box-shadow: none;
                color: #1e3a8a;
                font-size: 0.8rem;
                font-weight: 500;
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

        {{-- Yajra-generated init script --}}
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

        <script>
            function waitForTable(id, callback) {
                if (window.LaravelDataTables && window.LaravelDataTables[id]) {
                    callback(window.LaravelDataTables[id]);
                } else {
                    setTimeout(() => waitForTable(id, callback), 50);
                }
            }

            waitForTable('activitylogs-table', function (table) {
                const $length = $('#activitylogs-table_length').detach();
                $('#search-export-row').prepend($length);

                document.getElementById('activitylogs-search').addEventListener('input', function () {
                    table.search(this.value).draw();
                });

                const buttons = new $.fn.dataTable.Buttons(table, {
                    buttons: [
                        { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
                        { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } }
                    ]
                });

                const $btns = buttons.container().find('button');
                document.querySelector('[data-dt-button="excel"]').addEventListener('click', () => $btns.eq(0).click());
                document.querySelector('[data-dt-button="pdf"]').addEventListener('click', () => $btns.eq(1).click());
            });

            function showDetails(btn) {
                const data = JSON.parse(btn.dataset.log);
                document.getElementById('logMeta').textContent = btn.dataset.meta ?? '';

                function renderSection(label, values) {
                    if (!values || typeof values !== 'object' || Object.keys(values).length === 0) return '';

                    const isNew = label === 'New values';
                    const pillBg = isNew ? '#e6f1fb' : '#fcebeb';
                    const pillColor = isNew ? '#0c447c' : '#791f1f';

                    const fields = Object.entries(values).map(([key, val]) => {
                        const formattedKey = key.charAt(0).toUpperCase() + key.slice(1);

                        let items;
                        if (Array.isArray(val)) {
                            items = val.map(v => (v !== null && typeof v === 'object') ? JSON.stringify(v) : String(v ?? '—'));
                        } else if (val !== null && typeof val === 'object') {
                            items = Object.entries(val).map(([k, v]) => `${k}: ${v ?? '—'}`);
                        } else {
                            items = [String(val ?? '—')];
                        }

                        const pills = items.map(v =>
                            `<span style="font-size:12px;padding:3px 10px;background:${pillBg};color:${pillColor};">${v}</span>`
                        ).join('');

                        return `<div style="background:#f8fafc;border-radius:8px;border:1px solid #f1f5f9;padding:12px;margin-bottom:8px;">
                            <p style="margin:0 0 6px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:#94a3b8;">${formattedKey}</p>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">${pills}</div>
                        </div>`;
                    }).join('');

                    return `<div style="margin-bottom:12px;">
                        <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">${label}</p>
                        ${fields}
                    </div>`;
                }

                const newSection = renderSection('New values', data.attributes);
                const oldSection = renderSection('Old values', data.old);

                const placeholder = `<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;gap:8px;color:#94a3b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5" style="opacity:0.4;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <p style="margin:0;font-size:13px;color:#475569;">No changes recorded</p>
                    <p style="margin:0;font-size:12px;color:#94a3b8;">This activity has no attribute data attached.</p>
                </div>`;

                document.getElementById('logContent').innerHTML = (newSection || oldSection)
                    ? (newSection + oldSection)
                    : placeholder;

                document.getElementById('logModal').classList.remove('hidden');
                document.getElementById('logModal').classList.add('flex');
            }

            function closeModal() {
                document.getElementById('logModal').classList.add('hidden');
                document.getElementById('logModal').classList.remove('flex');
            }
        </script>
    @endpush
</x-app-layout>