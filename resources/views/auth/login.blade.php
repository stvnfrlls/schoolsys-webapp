<x-guest-layout>
    @section('title', 'Sign In')

    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-8 py-10">

                {{-- Header --}}
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-school-800 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                            <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
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