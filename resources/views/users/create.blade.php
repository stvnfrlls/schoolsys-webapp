<x-app-layout>
    @section('page-title', 'Create User')
    @section('breadcrumb', 'Add a new system user')

    {{-- Page card --}}
    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800 font-sans">New User</h2>
            <p class="text-xs text-slate-400 mt-0.5">Add a new system user with roles and permissions</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('name') border-red-500 @enderror"
                    placeholder="John Doe"
                    required>
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('email') border-red-500 @enderror"
                    placeholder="john@example.com"
                    required>
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('password') border-red-500 @enderror"
                    placeholder="••••••••"
                    required>
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Minimum 8 characters</p>
            </div>

            {{-- Password Confirmation --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600"
                    placeholder="••••••••"
                    required>
            </div>

            {{-- Roles --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-3">
                    Assign Roles <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2">
                    @forelse($roles as $role)
                    <div class="flex items-center">
                        <input
                            type="radio"
                            id="role-{{ $role->id }}"
                            name="role"
                            value="{{ $role->name }}"
                            class="w-4 h-4 text-school-600 border-slate-300 focus:ring-2 focus:ring-school-600">

                        <label for="role-{{ $role->id }}" class="ml-3 text-sm text-slate-700">
                            <span class="font-medium">{{ $role->name }}</span>

                            @if($role->description)
                            <span class="block text-xs text-slate-500">
                                {{ $role->description }}
                            </span>
                            @endif
                        </label>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">No roles available.</p>
                    @endforelse
                </div>
                @error('roles')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('status') border-red-500 @enderror">
                    <option value="">-- Select Status --</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Form actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Create User
                </button>
                <a href="{{ route('users.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
            </div>
        </form>

    </div>

</x-app-layout>