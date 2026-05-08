<x-app-layout>
    @section('page-title', 'Schedule Details')
    @section('breadcrumb', 'View class schedule details')

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

    {{-- Page card --}}
    <div class="bg-white rounded-xl border border-slate-200 max-w-3xl">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Schedule Details</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Viewing schedule record #{{ $schedule->id }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('schedules.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Back
                    </a>
                    @can('edit schedules')
                        <a href="{{ route('schedules.edit', $schedule) }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Edit
                        </a>
                    @endcan
                    @can('delete schedules')
                        <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" id="delete-schedule-form">
                            @csrf
                            @method('DELETE')
                        </form>
                        <button type="button" onclick="confirmDelete()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-500 transition-colors">
                            Delete
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Details body --}}
        <div class="p-6 space-y-6">

            {{-- Section 1: Academic Context --}}
            <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4 font-sans">Academic
                    Context</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- School Year --}}
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">School Year</p>
                        <p class="text-sm text-slate-800 font-medium">
                            {{ $schedule->schoolYear?->name ?? '—' }}
                        </p>
                    </div>

                    {{-- Section --}}
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Section</p>
                        <p class="text-sm text-slate-800 font-medium">
                            {{ $schedule->section?->gradeLevel?->name ?? '—' }}
                            @if ($schedule->section)
                                &mdash; {{ $schedule->section->name }}
                            @endif
                        </p>
                    </div>

                    {{-- Subject --}}
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Subject</p>
                        <p class="text-sm text-slate-800 font-medium">
                            {{ $schedule->subject?->name ?? '—' }}
                        </p>
                    </div>

                    {{-- Faculty --}}
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Faculty</p>
                        <p class="text-sm text-slate-800 font-medium">
                            @if ($schedule->faculty)
                                {{ $schedule->faculty->last_name }}, {{ $schedule->faculty->first_name }}
                            @else
                                —
                            @endif
                        </p>
                    </div>

                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 2: Schedule Details --}}
            <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4 font-sans">Schedule
                    Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Day --}}
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Day</p>
                        <p class="text-sm text-slate-800 font-medium">{{ $schedule->day_name }}</p>
                    </div>

                    {{-- Room --}}
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Room</p>
                        <p class="text-sm text-slate-800 font-medium">{{ $schedule->room ?? '—' }}</p>
                    </div>

                    {{-- Time Start --}}
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Time Start</p>
                        <p class="text-sm text-slate-800 font-medium">
                            {{ date('h:i A', strtotime($schedule->time_start)) }}
                        </p>
                    </div>

                    {{-- Time End --}}
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Time End</p>
                        <p class="text-sm text-slate-800 font-medium">
                            {{ date('h:i A', strtotime($schedule->time_end)) }}
                        </p>
                    </div>

                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 3: Metadata --}}
            <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4 font-sans">Record Info
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Created</p>
                        <p class="text-sm text-slate-800 font-medium">
                            {{ $schedule->created_at->format('M d, Y h:i A') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Last Updated</p>
                        <p class="text-sm text-slate-800 font-medium">
                            {{ $schedule->updated_at->format('M d, Y h:i A') }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                if (confirm('Are you sure you want to delete this schedule? This action cannot be undone.')) {
                    document.getElementById('delete-schedule-form').submit();
                }
            }
        </script>
    @endpush

</x-app-layout>