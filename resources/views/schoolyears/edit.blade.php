<x-app-layout>
    @section('page-title', 'Edit School Year')
    @section('breadcrumb', 'Update school year details')

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
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Edit School Year</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Update school year details</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('schoolyears.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button form="edit-schoolyear-form" type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-schoolyear-form" action="{{ route('schoolyears.update', $schoolyear) }}" method="POST"
            class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- School Year Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    School Year Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $schoolyear->name) }}"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('name') border-red-500 @enderror"
                    placeholder="e.g. 2024-2025" required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Must be unique across all school years</p>
            </div>

            {{-- Start Date --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-slate-700 mb-2">
                    Start Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="start_date" name="start_date"
                    value="{{ old('start_date', $schoolyear->start_date->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('start_date') border-red-500 @enderror"
                    required>
                @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">The date this school year officially begins</p>
            </div>

            {{-- End Date --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-slate-700 mb-2">
                    End Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="end_date" name="end_date"
                    value="{{ old('end_date', $schoolyear->end_date->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('end_date') border-red-500 @enderror"
                    required>
                @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">The date this school year officially ends</p>
            </div>

            {{-- Status --}}
            <div>
                <label for="is_active" class="block text-sm font-medium text-slate-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="is_active" name="is_active"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('is_active') border-red-500 @enderror">
                    <option value="active" {{ old('is_active', $schoolyear->is_active ? 'active' : '0') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('is_active', $schoolyear->is_active ? 'active' : 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Only one school year can be active at a time</p>
            </div>

            {{-- Record info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Created</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $schoolyear->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $schoolyear->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Last Updated</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $schoolyear->updated_at->format('M d, Y H:i') }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $schoolyear->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('delete-form').submit()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                    Delete School Year
                </button>
            </div>

        </form>

        {{-- Hidden delete form --}}
        <form id="delete-form" action="{{ route('schoolyears.destroy', $schoolyear) }}" method="POST"
            onsubmit="return confirm('Are you sure? This action cannot be undone.');">
            @csrf
            @method('DELETE')
        </form>

    </div>

</x-app-layout>