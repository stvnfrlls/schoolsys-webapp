<x-app-layout>
    @section('page-title', 'Permissions')
    @section('breadcrumb', 'Manage system permissions')

    {{-- Flash messages --}}
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

    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">
                        Edit Permission
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Update permission details
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('permissions.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                        Back
                    </a>
                    <button type="submit" type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-6">
                <div class="max-w-md flex flex-col gap-5">

                    <!-- Permission Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Permission Name
                        </label>
                        <input type="text" name="name" value="{{ old('name', $permission->name) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Guard Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Guard
                        </label>
                        <input type="text" name="guard_name" value="{{ old('guard_name', $permission->guard_name) }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('guard_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

        </div>
    </form>

</x-app-layout>