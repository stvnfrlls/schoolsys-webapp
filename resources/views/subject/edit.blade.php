<x-app-layout>
    @section('page-title', 'Edit Subject')
    @section('breadcrumb', 'Edit an existing subject')

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
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Edit Subject — {{ $subject->name }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Update the details below and save your changes</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('subjects.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button form="edit-subject-form" type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-subject-form" action="{{ route('subjects.update', $subject) }}" method="POST"
            class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Subject Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    Subject Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $subject->name) }}"
                    placeholder="e.g., English, Mathematics, Science"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('name') border-red-500 @enderror">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Code --}}
            <div>
                <label for="code" class="block text-sm font-medium text-slate-700 mb-2">
                    Subject Code <span class="text-red-500">*</span>
                </label>
                <input type="text" id="code" name="code" value="{{ old('code', $subject->code) }}"
                    placeholder="e.g., ENG101, MATH301"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('code') border-red-500 @enderror">
                @error('code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                    Description
                </label>
                <textarea id="description" name="description" rows="4"
                    placeholder="Optional: Provide a brief description of this subject..."
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('description') border-red-500 @enderror">{{ old('description', $subject->description) }}</textarea>
                @error('description')
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
                            {{ old('is_active', $subject->is_active) === 'active' ? 'checked' : '' }}
                            class="w-4 h-4 text-school-600 border-slate-300 focus:ring-2 focus:ring-school-600">
                        <span class="text-sm text-slate-700">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="inactive"
                            {{ old('is_active', $subject->is_active) === 'inactive' ? 'checked' : '' }}
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
                    <p class="text-sm text-slate-700 font-medium">{{ $subject->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $subject->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Last Updated</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $subject->updated_at->format('M d, Y H:i') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $subject->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('delete-subject-form').submit()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                    Delete Subject
                </button>
            </div>

        </form>

        {{-- Hidden delete form --}}
        <form id="delete-subject-form" action="{{ route('subjects.destroy', $subject) }}" method="POST"
            onsubmit="return confirm('Are you sure? This action cannot be undone.');">
            @csrf
            @method('DELETE')
        </form>

    </div>

</x-app-layout>