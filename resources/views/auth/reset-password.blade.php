<x-guest-layout>
    @section('title', 'Reset Password')

    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-8 py-10">

                {{-- Header --}}
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-school-800 mb-4">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="2.5" y="14.5" width="19" height="3.5" rx="1.5" fill="white" opacity="0.45"></rect>
          <rect x="2.5" y="10" width="19" height="3.5" rx="1.5" fill="white" opacity="0.70"></rect>
          <rect x="2.5" y="5.5" width="19" height="3.5" rx="1.5" fill="white"></rect>
          <circle cx="18.5" cy="7.25" r="2" fill="none" stroke="#0ea5e9" stroke-width="1.5"></circle>
        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Create new password</h2>
                    <p class="text-sm text-slate-500 mt-1">Enter your email and set a new password</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="mb-5">
                        <x-input-label for="email" :value="__('Email address')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="email"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="email"
                            name="email"
                            :value="old('email', $request->email)"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@school.edu" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <x-input-label for="password" :value="__('New password')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="password"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <x-input-label for="password_confirmation" :value="__('Confirm password')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="password_confirmation"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                    </div>

                    <!-- Submit -->
                    <x-primary-button class="w-full justify-center py-3 rounded-lg bg-school-800 hover:bg-school-700 text-sm font-semibold">
                        {{ __('Reset Password') }}
                    </x-primary-button>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>