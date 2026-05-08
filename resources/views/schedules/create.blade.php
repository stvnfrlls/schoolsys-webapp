<x-app-layout>
    @section('page-title', 'Create Schedule')
    @section('breadcrumb', 'Add a new class schedule')

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

        <form id="create-schedule-form" action="{{ route('schedules.store') }}" method="POST">
            @csrf

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">New Schedule</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Assign a subject and faculty to a section for a
                            specific day and time</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('schedules.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button form="create-schedule-form" type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Create Schedule
                        </button>
                    </div>
                </div>
            </div>

            {{-- Form body --}}
            <div class="p-6 space-y-6">

                {{-- Section 1: Academic Context --}}
                <div>
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4 font-sans">Academic Context
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- School Year --}}
                        <div>
                            <label for="school_year_id" class="block text-sm font-medium text-slate-700 mb-2">
                                School Year <span class="text-red-500">*</span>
                            </label>
                            <select id="school_year_id" name="school_year_id"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('school_year_id') border-red-500 @enderror">
                                <option value="">-- Select School Year --</option>
                                @foreach ($schoolYears as $schoolYear)
                                    <option value="{{ $schoolYear->id }}" {{ old('school_year_id') == $schoolYear->id ? 'selected' : '' }}>
                                        {{ $schoolYear->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_year_id')
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
                                        {{ $section->gradeLevel->name }} — {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-slate-700 mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <select id="subject_id" name="subject_id"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('subject_id') border-red-500 @enderror">
                                <option value="">-- Select Subject --</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Faculty --}}
                        <div>
                            <label for="faculty_id" class="block text-sm font-medium text-slate-700 mb-2">
                                Faculty <span class="text-red-500">*</span>
                            </label>
                            <select id="faculty_id" name="faculty_id"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('faculty_id') border-red-500 @enderror">
                                <option value="">-- Select Faculty --</option>
                                @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->last_name }}, {{ $faculty->first_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Section 2: Schedule Details --}}
                <div>
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4 font-sans">Schedule Details
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Day of Week --}}
                        <div>
                            <label for="day_of_week" class="block text-sm font-medium text-slate-700 mb-2">
                                Day <span class="text-red-500">*</span>
                            </label>
                            <select id="day_of_week" name="day_of_week"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('day_of_week') border-red-500 @enderror">
                                <option value="">-- Select Day --</option>
                                @foreach ([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('day_of_week') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('day_of_week')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Room --}}
                        <div>
                            <label for="room" class="block text-sm font-medium text-slate-700 mb-2">
                                Room
                                <span class="text-slate-400 font-normal">(optional)</span>
                            </label>
                            <input type="text" id="room" name="room" value="{{ old('room') }}"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('room') border-red-500 @enderror"
                                placeholder="e.g. Room 101, Lab A">
                            @error('room')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Time Start --}}
                        <div>
                            <label for="time_start" class="block text-sm font-medium text-slate-700 mb-2">
                                Time Start <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="time_start" name="time_start" value="{{ old('time_start') }}"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('time_start') border-red-500 @enderror">
                            @error('time_start')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Time End --}}
                        <div>
                            <label for="time_end" class="block text-sm font-medium text-slate-700 mb-2">
                                Time End <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="time_end" name="time_end" value="{{ old('time_end') }}"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('time_end') border-red-500 @enderror">
                            @error('time_end')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-500">Must be later than the start time</p>
                        </div>

                    </div>
                </div>

            </div>
        </form>

    </div>

</x-app-layout>