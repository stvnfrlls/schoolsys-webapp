@php
    $statusStyles = [
        'active' => ['badge' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-600'],
        'enrolled' => ['badge' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-600'],
        'graduated' => ['badge' => 'bg-blue-100 text-blue-700', 'dot' => 'bg-blue-600'],
        'dropped' => ['badge' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-600'],
        'transferee' => ['badge' => 'bg-amber-100 text-amber-700', 'dot' => 'bg-amber-600'],
    ];
    $style = $statusStyles[$status ?? ''] ?? ['badge' => 'bg-slate-100 text-slate-700', 'dot' => 'bg-slate-400'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $style['badge'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
    {{ ucfirst($status) }}
</span>