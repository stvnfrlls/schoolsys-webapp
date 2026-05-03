<x-app-layout>
    @section('page-title', 'Assign Subject to Grade Level')
    @section('breadcrumb', 'Create a new subject assignment')

    {{-- Flash messages --}}
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

    {{-- Page card --}}
    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">
        <form action="{{ route('subjectperlevel.store') }}" method="POST">
            @csrf

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">Assign Subject to Grade Level</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Pick a subject, then assign a grade level and hours</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('subjectperlevel.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Assign Subject
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Subject (primary entity) --}}
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-slate-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="subject_id"
                        name="subject_id"
                        class="w-full px-4 py-2 text-sm border @error('subject_id') border-red-300 bg-red-50 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 bg-white text-slate-900"
                        required>
                        <option value="" disabled selected>-- Select Subject --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                                {{ $subject->name }} ({{ $subject->code }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Only active subjects are listed</p>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Grade Level --}}
                <div>
                    <label for="gradelevel_id" class="block text-sm font-medium text-slate-700 mb-2">
                        Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="gradelevel_id"
                        name="gradelevel_id"
                        class="w-full px-4 py-2 text-sm border @error('gradelevel_id') border-red-300 bg-red-50 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 bg-white text-slate-900"
                        required>
                        <option value="" disabled selected>-- Select Grade Level --</option>
                        @foreach ($gradeLevels as $gradeLevel)
                            <option value="{{ $gradeLevel->id }}" @selected(old('gradelevel_id') == $gradeLevel->id)>
                                {{ $gradeLevel->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('gradelevel_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hours Per Week --}}
                <div>
                    <label for="hours_per_week" class="block text-sm font-medium text-slate-700 mb-2">
                        Hours Per Week
                    </label>
                    <input
                        type="number"
                        id="hours_per_week"
                        name="hours_per_week"
                        min="0"
                        max="99"
                        step="0.5"
                        value="{{ old('hours_per_week') }}"
                        placeholder="e.g., 3"
                        class="w-full px-4 py-2 text-sm border @error('hours_per_week') border-red-300 bg-red-50 @else border-slate-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 bg-white text-slate-900">
                    @error('hours_per_week')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-3">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="active"
                                {{ old('is_active', 'active') === 'active' ? 'checked' : '' }}
                                class="w-4 h-4 text-school-600 border-slate-300 focus:ring-2 focus:ring-school-600">
                            <span class="text-sm text-slate-700">Active</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="inactive"
                                {{ old('is_active') === 'inactive' ? 'checked' : '' }}
                                class="w-4 h-4 text-school-600 border-slate-300 focus:ring-2 focus:ring-school-600">
                            <span class="text-sm text-slate-700">Inactive</span>
                        </label>
                    </div>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </form>
    </div>

</x-app-layout>