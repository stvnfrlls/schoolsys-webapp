<x-app-layout>
    @section('page-title', 'Edit User')
    @section('breadcrumb', 'Update user information and roles')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main form --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200">

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800 font-sans">Edit User</h2>
                <p class="text-xs text-slate-400 mt-0.5">Update user information and roles</p>
            </div>

            {{-- Form --}}
            <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('name') border-red-500 @enderror"
                        placeholder="John Doe"
                        required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email (read-only) --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        value="{{ $user->email }}"
                        class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-slate-50 text-slate-600 cursor-not-allowed"
                        disabled>
                    <p class="mt-1 text-xs text-slate-500">Email cannot be changed</p>
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
                                {{ $user->roles->contains($role->id) ? 'checked' : '' }}
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
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        Save Changes
                    </button>
                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- User info card --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 font-sans">User Info</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Joined</p>
                        <p class="text-sm text-slate-700">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Last updated</p>
                        <p class="text-sm text-slate-700">{{ $user->updated_at->format('M d, Y \a\t H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Current status</p>
                        <div class="inline-block mt-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Change password card --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 font-sans">Change Password</h3>
                <form action="{{ route('users.update-password', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-slate-700 mb-2">
                            New Password
                        </label>
                        <input
                            type="password"
                            id="new_password"
                            name="password"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('password') border-red-500 @enderror"
                            placeholder="••••••••">
                        @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-500">Leave empty to keep current password</p>
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                            Confirm Password
                        </label>
                        <input
                            type="password"
                            id="new_password_confirmation"
                            name="password_confirmation"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600"
                            placeholder="••••••••">
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Update Password
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-red-200 p-6">
                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                        Delete User
                    </button>
                </form>
            </div>

        </div>

    </div>

</x-app-layout>