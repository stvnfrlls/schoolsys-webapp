@props([
    'tableId' => 'table',
    'excel'   => true,
    'pdf'     => true,
])

<div id="dt-buttons" class="flex items-center gap-2 shrink-0">

    @if ($excel)
    <button type="button" data-dt-button="excel"
        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
        </svg>
        <span class="hidden lg:inline">Excel</span>
    </button>
    @endif

    @if ($pdf)
    <button type="button" data-dt-button="pdf"
        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
        </svg>
        <span class="hidden lg:inline">PDF</span>
    </button>
    @endif

</div>