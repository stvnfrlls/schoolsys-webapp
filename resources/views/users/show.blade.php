<x-app-layout>
    @section('page-title', $user->name)
    @section('breadcrumb', 'View user details')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main profile card --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200">

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">User Profile</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Complete user information</p>
                    </div>
                    <a href="{{ route('users.edit', $user) }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Edit
                    </a>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-6 space-y-6">

                {{-- Name & Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Full Name</p>
                        <p class="text-sm text-slate-700 font-medium">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Email</p>
                        <a href="mailto:{{ $user->email }}" class="text-sm text-school-600 hover:text-school-700 font-medium">
                            {{ $user->email }}
                        </a>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Status</p>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                            <span class="w-2 h-2 rounded-full {{ $user->status === 'active' ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>

                {{-- Roles --}}
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">Assigned Roles</p>
                    @if($user->roles->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium">{{ $role->name }}</span>
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-slate-500">No roles assigned</p>
                    @endif
                </div>

                {{-- Permissions count (if available) --}}
                @if($user->roles->count())
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">Permissions</p>
                    <div class="text-sm text-slate-700">
                        <p>This user has <span class="font-semibold text-school-600">{{ $user->getPermissionsViaRoles()->unique('id')->count() }}</span> permissions across assigned roles.</p>
                    </div>
                </div>
                @endif

            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Account info card --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 font-sans">Account Info</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Account created</p>
                        <p class="text-sm text-slate-700 font-medium">{{ $user->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-slate-400">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Last updated</p>
                        <p class="text-sm text-slate-700 font-medium">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                        <p class="text-xs text-slate-400">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Role summary card --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 font-sans">Role Summary</h3>
                @if($user->roles->count())
                <div class="space-y-3">
                    @foreach($user->roles as $role)
                    <div class="pb-3 border-b border-slate-100 last:border-0 last:pb-0">
                        <p class="text-sm font-medium text-slate-700">{{ $role->name }}</p>
                        @if($role->description)
                        <p class="text-xs text-slate-500 mt-1">{{ $role->description }}</p>
                        @endif
                        <p class="text-xs text-slate-400 mt-1.5">
                            {{ $role->permissions->count() }} permissions
                        </p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-500">No roles assigned yet</p>
                @endif
            </div>

            {{-- Actions card --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 font-sans">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('users.edit', $user) }}"
                        class="flex items-center gap-2 w-full px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Edit User
                    </a>
                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-2 w-full px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Back to Users
                    </a>
                </div>
            </div>

        </div>

    </div>

</x-app-layout>