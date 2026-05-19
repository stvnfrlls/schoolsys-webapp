<x-app-layout>
    @section('page-title', 'Edit Attendance')
    @section('breadcrumb', 'Correct a single student attendance entry')

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
        <div class="flex items-center justify-between px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-semibold text-slate-800 font-sans">Edit Attendance Record</h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Last updated {{ $attendance->updated_at->diffForHumans() }}
                </p>
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

        {{-- Read-only context --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-slate-100 border-b border-slate-100">
            <div class="bg-white px-4 sm:px-6 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Student</p>
                <p class="text-sm font-semibold text-slate-800">
                    {{ $attendance->student->last_name }}, {{ $attendance->student->first_name }}
                </p>
                <p class="text-xs text-slate-400 font-mono mt-0.5">
                    {{ $attendance->student->student_number }}
                </p>
            </div>
            <div class="bg-white px-4 sm:px-6 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Date</p>
                <p class="text-sm font-semibold text-slate-800">
                    {{ $attendance->date->format('F d, Y') }}
                </p>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ \Carbon\Carbon::parse($attendance->schedule->time_start)->format('g:i A') }}
                    –
                    {{ \Carbon\Carbon::parse($attendance->schedule->time_end)->format('g:i A') }}
                </p>
            </div>
            <div class="bg-white px-4 sm:px-6 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Subject</p>
                <p class="text-sm text-slate-700">
                    {{ $attendance->schedule->subject->name ?? '—' }}
                </p>
            </div>
            <div class="bg-white px-4 sm:px-6 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Section</p>
                <p class="text-sm text-slate-700">
                    {{ $attendance->schedule->section->gradeLevel->name ?? '' }}
                    {{ $attendance->schedule->section->name ?? '—' }}
                </p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('attendance.update', $attendance) }}">
            @csrf
            @method('PUT')

            <div class="px-4 sm:px-6 py-6 space-y-6">

                {{-- Status --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Status</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([
                            'present' => ['peer-checked:bg-emerald-100', 'peer-checked:text-emerald-800', 'peer-checked:border-emerald-300'],
                            'late'    => ['peer-checked:bg-amber-100',   'peer-checked:text-amber-800',   'peer-checked:border-amber-300'],
                            'absent'  => ['peer-checked:bg-red-100',     'peer-checked:text-red-800',     'peer-checked:border-red-300'],
                            'excused' => ['peer-checked:bg-blue-100',    'peer-checked:text-blue-800',    'peer-checked:border-blue-300'],
                        ] as $s => [$pBg, $pText, $pBorder])
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="{{ $s }}"
                                    class="sr-only peer"
                                    {{ old('status', $attendance->status) === $s ? 'checked' : '' }}>
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm border
                                    border-slate-200 text-slate-400 bg-white
                                    hover:bg-slate-50 hover:text-slate-600
                                    {{ $pBg }} {{ $pText }} {{ $pBorder }}
                                    peer-checked:font-semibold transition-colors">
                                    {{ ucfirst($s) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('status')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remarks --}}
                <div>
                    <label for="remarks"
                        class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                        Remarks
                        <span class="normal-case tracking-normal font-normal text-slate-300 ml-1">(optional)</span>
                    </label>
                    <textarea id="remarks" name="remarks" rows="3"
                        placeholder="e.g. Medical certificate submitted"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700
                               placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-school-600
                               focus:border-school-600 transition-colors
                               @error('remarks') border-red-400 @enderror">{{ old('remarks', $attendance->remarks) }}</textarea>
                    @error('remarks')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Footer actions --}}
            <div class="flex items-center justify-end gap-3 px-4 sm:px-6 py-4 border-t border-slate-100">
                <a href="{{ route('attendance.index') }}"
                    class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Save Changes
                </button>
            </div>

        </form>

    </div>

</x-app-layout>