<x-app-layout>
    @section('page-title', 'Create Section')
    @section('breadcrumb', 'Add a new section to a grade level')

    {{-- Flash messages --}}
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

    {{-- Page card --}}
    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">
        <form action="{{ route('sections.store') }}" method="POST">
            @csrf
            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">New Section</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Fill in the details below to create a new section</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('sections.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Create Section
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Grade Level --}}
                <div>
                    <label for="grade_level_id" class="block text-sm font-medium text-slate-700 mb-2">
                        Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select id="grade_level_id" name="grade_level_id"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('grade_level_id') border-red-500 @enderror">
                        <option value="" disabled selected>Select a grade level</option>
                        @foreach ($gradeLevels as $gradeLevel)
                            <option value="{{ $gradeLevel->id }}" {{ old('grade_level_id') == $gradeLevel->id ? 'selected' : '' }}>
                                {{ $gradeLevel->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('grade_level_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Section Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Section Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        placeholder="e.g. Rizal, Bonifacio"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('name') border-red-500 @enderror">
                    @error('name')
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
                            <input type="radio" name="is_active" value="active" {{ old('is_active', 'active') === 'active' ? 'checked' : '' }}
                                class="w-4 h-4 text-school-600 border-slate-300 focus:ring-2 focus:ring-school-600">
                            <span class="text-sm text-slate-700">Active</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="inactive" {{ old('is_active') === 'inactive' ? 'checked' : '' }}
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