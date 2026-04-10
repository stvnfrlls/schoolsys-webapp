<x-guest-layout>
    @section('title', 'Sign In')

    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-8 py-10">

                {{-- Header --}}
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-school-800 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
                    <p class="text-sm text-slate-500 mt-1">Sign in to your account to continue</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

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