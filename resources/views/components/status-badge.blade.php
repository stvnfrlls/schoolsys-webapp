{{-- Status badge with color and indicator --}}
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $status === 'active' ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
    {{ ucfirst($status) }}
</span>