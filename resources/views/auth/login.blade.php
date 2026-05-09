<x-guest-layout>
    @section('title', 'Sign In')

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
                    <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
                    <p class="text-sm text-slate-500 mt-1">Sign in to your account to continue</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-5">
                        <x-input-label for="email" :value="__('Email address')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="email"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@school.edu" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-1.5">
                            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-slate-700" />
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs text-school-700 hover:text-school-600 font-medium transition-colors">
                                Forgot password?
                            </a>
                            @endif
                        </div>
                        <x-text-input
                            id="password"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2 mb-6">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="rounded border-slate-300 text-school-700 focus:ring-school-600 w-4 h-4">
                        <label for="remember_me" class="text-sm text-slate-600">Keep me signed in</label>
                    </div>

                    <!-- Submit -->
                    <x-primary-button class="w-full justify-center py-3 rounded-lg bg-school-800 hover:bg-school-700 text-sm font-semibold">
                        {{ __('Sign In') }}
                    </x-primary-button>
                </form>

                @if (Route::has('register'))
                <p class="text-center text-sm text-slate-500 mt-6">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-school-700 font-medium hover:text-school-600 transition-colors">
                        Register here
                    </a>
                </p>
                @endif

            </div>
        </div>
    </div>
</x-guest-layout>