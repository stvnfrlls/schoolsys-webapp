<x-app-layout>
    @section('page-title', 'Create Enrollment')
    @section('breadcrumb', 'Enroll a student into a section')

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
    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">

        <form id="create-enrollment-form" action="{{ route('enrollments.store') }}" method="POST">
            @csrf

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">New Enrollment</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Assign a student to a section for a given school year
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('enrollments.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button form="create-enrollment-form" type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Create Enrollment
                        </button>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="p-6 space-y-6">

                {{-- Student --}}
                <div>
                    <label for="student_id" class="block text-sm font-medium text-slate-700 mb-2">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select id="student_id" name="student_id"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('student_id') border-red-500 @enderror">
                        <option value="">-- Select Student --</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Section --}}
                <div>
                    <label for="section_id" class="block text-sm font-medium text-slate-700 mb-2">
                        Section <span class="text-red-500">*</span>
                    </label>
                    <select id="section_id" name="section_id"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('section_id') border-red-500 @enderror">
                        <option value="">-- Select Section --</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('section_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- School Year --}}
                <div>
                    <label for="school_year_id" class="block text-sm font-medium text-slate-700 mb-2">
                        School Year <span class="text-red-500">*</span>
                    </label>
                    <select id="school_year_id" name="school_year_id"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('school_year_id') border-red-500 @enderror">
                        <option value="">-- Select School Year --</option>
                        @foreach ($schoolYears as $schoolyear)
                            <option value="{{ $schoolyear->id }}" {{ old('school_year_id') == $schoolyear->id ? 'selected' : '' }}>
                                {{ $schoolyear->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_year_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Enrolled At --}}
                <div>
                    <label for="enrolled_at" class="block text-sm font-medium text-slate-700 mb-2">
                        Enrollment Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="enrolled_at" name="enrolled_at"
                        value="{{ old('enrolled_at', now()->toDateString()) }}"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('enrolled_at') border-red-500 @enderror">
                    @error('enrolled_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Defaults to today if not changed</p>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                        Status
                    </label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('status') border-red-500 @enderror">
                        <option value="enrolled" {{ old('status', 'enrolled') === 'enrolled' ? 'selected' : '' }}>Enrolled
                        </option>
                        <option value="dropped" {{ old('status') === 'dropped' ? 'selected' : '' }}>Dropped</option>
                        <option value="transferred" {{ old('status') === 'transferred' ? 'selected' : '' }}>Transferred
                        </option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Defaults to <strong>Enrolled</strong> — change only if
                        backdating a record</p>
                </div>

            </div>
        </form>

    </div>

</x-app-layout>