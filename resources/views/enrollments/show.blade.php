<x-app-layout>
    @section('page-title', $enrollment->student->full_name)
    @section('breadcrumb', 'View enrollment details')

    {{-- Flash messages --}}
    @if (session('success'))
        <div
            class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Enrollment Details</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Complete enrollment information</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('enrollments.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Back
                    </a>
                    <a href="{{ route('enrollments.edit', $enrollment) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Edit
                    </a>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6 space-y-6">

            {{-- Student & Section --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Student</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $enrollment->student->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Section</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $enrollment->section->name }}</p>
                </div>
            </div>

            {{-- School Year & Enrolled At --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">School Year</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $enrollment->schoolYear->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Enrollment Date</p>
                    <p class="text-sm text-slate-700 font-medium">
                        {{ \Carbon\Carbon::parse($enrollment->enrolled_at)->format('M d, Y') }}
                    </p>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Status</p>
                @php
                    $statusConfig = [
                        'enrolled' => ['bg-emerald-100 text-emerald-700', 'bg-emerald-600', 'Enrolled'],
                        'dropped' => ['bg-red-100 text-red-700', 'bg-red-500', 'Dropped'],
                        'transferred' => ['bg-amber-100 text-amber-700', 'bg-amber-500', 'Transferred'],
                        'completed' => ['bg-blue-100 text-blue-700', 'bg-blue-500', 'Completed'],
                    ];
                    [$badgeClass, $dotClass, $label] = $statusConfig[$enrollment->status] ?? ['bg-slate-100 text-slate-700', 'bg-slate-400', ucfirst($enrollment->status)];
                @endphp
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium {{ $badgeClass }}">
                    <span class="w-2 h-2 rounded-full {{ $dotClass }}"></span>
                    {{ $label }}
                </span>
            </div>

            {{-- Record info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Created</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $enrollment->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $enrollment->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Last Updated</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $enrollment->updated_at->format('M d, Y H:i') }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $enrollment->updated_at->diffForHumans() }}</p>
                </div>
            </div>

        </div>

    </div>

</x-app-layout>