<x-app-layout>
    @section('page-title', 'Edit Grade Level')
    @section('breadcrumb', 'Update grade level details')

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

    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Edit Grade Level</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Update grade level details</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('gradelevels.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button form="edit-gradelevel-form" type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-gradelevel-form" action="{{ route('gradelevels.update', $gradeLevel) }}" method="POST"
            class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Grade Level Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    Grade Level Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $gradeLevel->name) }}"
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
                <input type="number" id="level" name="level" value="{{ old('level', $gradeLevel->level) }}" min="1"
                    max="255"
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
                    <option value="active" {{ old('is_active', $gradeLevel->is_active) === 'active' ? 'selected' : '' }}>
                        Active</option>
                    <option value="inactive" {{ old('is_active', $gradeLevel->is_active) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Make this grade level available for use</p>
            </div>

            {{-- Record info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Created</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $gradeLevel->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $gradeLevel->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Last Updated</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $gradeLevel->updated_at->format('M d, Y H:i') }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $gradeLevel->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('delete-form').submit()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                    Delete Grade Level
                </button>
            </div>

        </form>

        {{-- Hidden delete form --}}
        <form id="delete-form" action="{{ route('gradelevels.destroy', $gradeLevel) }}" method="POST"
            onsubmit="return confirm('Are you sure? This action cannot be undone.');">
            @csrf
            @method('DELETE')
        </form>

    </div>

</x-app-layout>