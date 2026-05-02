<!-- It is quality rather than quantity that matters. - Lucius Annaeus Seneca -->
<x-app-layout>
    @section('page-title', 'Section Details')
    @section('breadcrumb', 'View section information')

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
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Section Details</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Complete section information and assignment</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('sections.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Back
                    </a>
                    <a href="{{ route('sections.edit', $section) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Edit
                    </a>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6 space-y-6">

            {{-- Grade Level & Section Name --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Grade Level</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $section->gradeLevel->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Section Name</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $section->name }}</p>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Status</p>
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium
                    {{ $section->is_active === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                    <span class="w-2 h-2 rounded-full
                        {{ $section->is_active === 'active' ? 'bg-emerald-600' : 'bg-slate-400' }}">
                    </span>
                    {{ $section->is_active === 'active' ? 'Active' : 'Inactive' }}
                </span>
            </div>

            {{-- Record info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Created</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $section->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $section->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Last Updated</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $section->updated_at->format('M d, Y H:i') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $section->updated_at->diffForHumans() }}</p>
                </div>
            </div>

        </div>

    </div>

</x-app-layout>