<x-app-layout>
    @section('page-title', 'Faculty Details')
    @section('breadcrumb', 'View faculty record')

    @if (session('success'))
        <div
            class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 mb-6">
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
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">{{ $faculty->full_name }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $faculty->employee_number }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('faculty.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Back
                    </a>
                    @can('edit faculty')
                        <a href="{{ route('faculty.edit', $faculty) }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Edit
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="p-6 space-y-12">

            {{-- Section: Portal Account --}}
            <div>
                <div class="py-3 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Portal Account
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Login credentials for the faculty portal</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Employee Number
                        </p>
                        <p class="text-sm text-slate-700">{{ $faculty->employee_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Email Address</p>
                        <p class="text-sm text-slate-700">{{ $faculty->user?->email ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Section: Personal Information --}}
            <div>
                <div class="py-3 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Personal
                        Information</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Faculty member's basic personal details</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">First Name</p>
                        <p class="text-sm text-slate-700">{{ $faculty->first_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Middle Name</p>
                        <p class="text-sm text-slate-700">{{ $faculty->middle_name ?? '—' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Last Name</p>
                        <p class="text-sm text-slate-700">{{ $faculty->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Birth Date</p>
                        <p class="text-sm text-slate-700">
                            {{ $faculty->birth_date ? $faculty->birth_date->format('F d, Y') : '—' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Gender</p>
                        <p class="text-sm text-slate-700 capitalize">{{ $faculty->gender ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Contact Number
                        </p>
                        <p class="text-sm text-slate-700">{{ $faculty->contact_number ?? '—' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Address</p>
                    <p class="text-sm text-slate-700">{{ $faculty->address ?? '—' }}</p>
                </div>
            </div>

            {{-- Section: Academic Information --}}
            <div>
                <div class="py-3 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Academic
                        Information</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Department, rank, and area of specialization</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Department</p>
                        <p class="text-sm text-slate-700">{{ $faculty->department ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Position</p>
                        <p class="text-sm text-slate-700">{{ $faculty->position ?? '—' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Academic Rank</p>
                        <p class="text-sm text-slate-700">
                            {{ $faculty->rank ? str($faculty->rank)->replace('_', ' ')->title() : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Specialization
                        </p>
                        <p class="text-sm text-slate-700">{{ $faculty->specialization ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Section: Employment --}}
            <div>
                <div class="py-3 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Employment</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Employment type and current status</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Employment Type
                        </p>
                        <p class="text-sm text-slate-700">
                            {{ $faculty->employment_type ? str($faculty->employment_type)->replace('_', ' ')->title() : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Status</p>
                        @include('components.status-badge', ['status' => $faculty->status])
                    </div>
                </div>
            </div>

            {{-- Section: Record Info --}}
            <div>
                <div class="py-3 border-b border-slate-100 mb-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Record Info
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Date Added</p>
                        <p class="text-sm text-slate-700">{{ $faculty->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">Last Updated</p>
                        <p class="text-sm text-slate-700">{{ $faculty->updated_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>