<x-app-layout>
    @section('page-title', 'Permissions')
    @section('breadcrumb', 'Manage system permissions')

    {{-- Flash Messages --}}
    @if (session('success'))
        <div
            class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m0 3.75h.008v.008H12v-.008Zm9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-800 font-sans">
                    Viewing Permission
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    Permission details and assigned roles
                </p>
            </div>

            <a href="{{ route('permissions.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                Back
            </a>
        </div>

        {{-- Content --}}
        <div class="p-6 space-y-6">

            {{-- Permission Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Permission Name
                </label>
                <input type="text" value="{{ $permission->name }}" disabled
                    class="w-50 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
            </div>

            {{-- Assigned Roles --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Assigned Roles
                </label>

                @if($permission->roles->isEmpty())
                    <p class="text-sm text-slate-400 italic">No roles assigned to this permission.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($permission->roles as $role)
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium border
                                                {{ $role->name === 'admin'
                            ? 'bg-rose-50 border-rose-200 text-rose-700'
                            : 'bg-indigo-50 border-indigo-200 text-indigo-700' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $role->name }}
                                    </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Meta Info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs text-slate-400 mb-1">Created At</p>
                    <p class="text-sm text-slate-700">{{ $permission->created_at->format('F j, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Last Updated</p>
                    <p class="text-sm text-slate-700">{{ $permission->updated_at->format('F j, Y h:i A') }}</p>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>