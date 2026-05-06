<x-app-layout>
    @section('page-title', 'Student Details')
    @section('breadcrumb', 'View student record')

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

    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">
                        {{ $student->first_name }} {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $student->student_number }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('students.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Back
                    </a>
                    <a href="{{ route('students.edit', $student) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-10">

            {{-- Section: Portal Account --}}
            <div class="pb-4">
                <div class="pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Portal Account</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Login credentials for the student portal</p>
                </div>

                <dl class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Student Number</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->student_number }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Email Address</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->user->email }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Section: Personal Information --}}
            <div class="pb-4">
                <div class="py-3 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Personal Information</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Student's basic personal details</p>
                </div>

                <dl class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">First Name</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->first_name }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Middle Name</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->middle_name ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Last Name</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->last_name }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Birth Date</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">
                            {{ $student->birth_date ? $student->birth_date->format('F d, Y') : '—' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Gender</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700 capitalize">{{ $student->gender ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Contact Number</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->contact_number ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Address</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700 leading-relaxed">{{ $student->address ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Section: Guardian Information --}}
            <div class="pb-4">
                <div class="py-3 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Guardian Information</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Parent or guardian contact details</p>
                </div>

                <dl class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Guardian Name</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->guardian_name ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Contact</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->guardian_contact ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Relationship</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">{{ $student->guardian_relationship ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Section: Enrollment Status --}}
            <div class="pb-4">
                <div class="pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Enrollment</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Assign the student to a school year and section</p>
                </div>

                <dl class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">School Year</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">
                            {{ $student->enrollment?->schoolYear?->label ?? '—' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Grade Level &
                            Section</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">
                            @if ($student->enrollment?->section)
                                {{ $student->enrollment->section->gradeLevel->name ?? 'No Grade' }} —
                                {{ $student->enrollment->section->name }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Status</dt>
                        <dd class="sm:col-span-2">
                            @php
                                $statusStyles = [
                                    'active' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'graduated'  => 'bg-blue-50 text-blue-700 border border-blue-200',
                                    'inactive'   => 'bg-slate-50 text-slate-600 border border-slate-200',
                                ];
                                $style = $statusStyles[$student->status] ?? 'bg-slate-50 text-slate-600 border border-slate-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium capitalize {{ $style }}">
                                {{ $student->status ?? '—' }}
                            </span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Enrolled At</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">
                            {{ $student->created_at->format('F d, Y') }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Last Updated</dt>
                        <dd class="sm:col-span-2 text-sm text-slate-700">
                            {{ $student->updated_at->format('F d, Y \a\t g:i A') }}
                        </dd>
                    </div>
                </dl>
            </div>

        </div>

        {{-- Card footer: danger zone --}}
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 mt-0.5">Permanently delete this student record</p>
                </div>
                <form action="{{ route('students.destroy', $student) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete {{ addslashes($student->first_name . ' ' . $student->last_name) }}? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition-colors">
                        Delete Student
                    </button>
                </form>
            </div>
        </div>

    </div>

</x-app-layout>