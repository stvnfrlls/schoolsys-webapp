<x-app-layout>
    @section('page-title', 'Create Grade Level')
    @section('breadcrumb', 'Add a new grade level')

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

        <form action="{{ route('gradelevels.store') }}" method="POST">
            @csrf
            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">New Grade Level</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Add a new grade level for use across the system</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('gradelevels.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button form="edit-gradelevel-form" type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Create Grade Level
                        </button>
                    </div>
                </div>
            </div>
    
            {{-- Form --}}
            <div class="p-6 space-y-6">
    
    
                {{-- Grade Level Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Grade Level Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('name') border-red-500 @enderror"
                        placeholder="e.g. Grade 1, Grade 11, Kindergarten" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
    
                {{-- Level (Order) --}}
                <div>
                    <label for="level" class="block text-sm font-medium text-slate-700 mb-2">
                        Level (Order) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="level" name="level" value="{{ old('level') }}" min="1" max="255"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('level') border-red-500 @enderror"
                        placeholder="e.g. 1, 2, 3" required>
                    @error('level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Determines the display order of this grade level</p>
                </div>
    
                {{-- Status --}}
                <div>
                    <label for="is_active" class="block text-sm font-medium text-slate-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="is_active" name="is_active"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('is_active') border-red-500 @enderror">
                        <option value="">-- Select Status --</option>
                        <option value="active" {{ old('is_active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('is_active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Make this grade level available for use</p>
                </div>
    
            </div>
        </form>

    </div>

</x-app-layout>