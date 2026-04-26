<x-app-layout>
    @section('page-title', 'Roles')
    @section('breadcrumb', 'Manage system roles and their permissions')

    {{-- Flash messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008Zm9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Main Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">

                <!-- Left: Title -->
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">
                        Edit {{ $role->name }} Role
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Update role and assigned permissions
                    </p>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('roles.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700
                               hover:bg-slate-100 transition">
                        Back
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        Save
                    </button>
                </div>

            </div>

            {{-- Content --}}
            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Role Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Role Name
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $role->name) }}"
                            class="w-50 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">

                        @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Permissions
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-[400px] overflow-y-auto pr-2">
                            @foreach($permissions as $permission)
                            <label class="flex items-center gap-2 text-sm text-gray-700 p-1">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    class="rounded border-gray-300 text-indigo-600"
                                    {{ in_array($permission->name, old('permissions', $role->permissions->pluck('name')->toArray())) ? 'checked' : '' }}>
                                <span>{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>

                        @error('permissions')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

        </div>
    </form>

</x-app-layout>