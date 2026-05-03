<x-app-layout>
    @section('page-title', 'Edit Subject Assignment')
    @section('breadcrumb', 'Edit an existing assignment')

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

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">
                        Edit Assignment — {{ $subjectperlevel->subject->name }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Update the details below and save your changes</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('subjectperlevel.show', $subjectperlevel) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button form="edit-assignment-form" type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-assignment-form" action="{{ route('subjectperlevel.update', $subjectperlevel) }}" method="POST"
            class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Subject (primary entity) --}}
            <div>
                <label for="subject_id" class="block text-sm font-medium text-slate-700 mb-2">
                    Subject <span class="text-red-500">*</span>
                </label>
                <select id="subject_id" name="subject_id" required
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 bg-white text-slate-900 @error('subject_id') border-red-500 @enderror">
                    <option value="">-- Select Subject --</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}"
                            @selected(old('subject_id', $subjectperlevel->subject_id) == $subject->id)>
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
                <select id="gradelevel_id" name="gradelevel_id" required
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 bg-white text-slate-900 @error('gradelevel_id') border-red-500 @enderror">
                    <option value="">-- Select Grade Level --</option>
                    @foreach ($gradeLevels as $gradeLevel)
                        <option value="{{ $gradeLevel->id }}"
                            @selected(old('gradelevel_id', $subjectperlevel->gradelevel_id) == $gradeLevel->id)>
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
                <input type="number" id="hours_per_week" name="hours_per_week"
                    min="0" max="99" step="0.5"
                    placeholder="e.g., 3"
                    value="{{ old('hours_per_week', $subjectperlevel->hours_per_week) }}"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('hours_per_week') border-red-500 @enderror">
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
                            {{ old('is_active', $subjectperlevel->is_active) === 'active' ? 'checked' : '' }}
                            class="w-4 h-4 text-school-600 border-slate-300 focus:ring-2 focus:ring-school-600">
                        <span class="text-sm text-slate-700">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="inactive"
                            {{ old('is_active', $subjectperlevel->is_active) === 'inactive' ? 'checked' : '' }}
                            class="w-4 h-4 text-school-600 border-slate-300 focus:ring-2 focus:ring-school-600">
                        <span class="text-sm text-slate-700">Inactive</span>
                    </label>
                </div>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Record info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Created</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $subjectperlevel->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $subjectperlevel->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Last Updated</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $subjectperlevel->updated_at->format('M d, Y H:i') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $subjectperlevel->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('delete-assignment-form').submit()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                    Delete Assignment
                </button>
            </div>

        </form>

        {{-- Hidden delete form --}}
        <form id="delete-assignment-form" action="{{ route('subjectperlevel.destroy', $subjectperlevel) }}" method="POST"
            onsubmit="return confirm('Are you sure? This action cannot be undone.');">
            @csrf
            @method('DELETE')
        </form>

    </div>

</x-app-layout>