<x-guest-layout>
    @section('title', 'Create Account')

    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-8 py-10">

                {{-- Header --}}
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-school-800 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Create your account</h2>
                    <p class="text-sm text-slate-500 mt-1">Get started with SchoolSys today</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-5">
                        <x-input-label for="name" :value="__('Full Name')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="name"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="text"
                            name="name"
                            :value="old('name')"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Juan dela Cruz" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                    </div>

                    <!-- Email Address -->
                    <div class="mb-5">
                        <x-input-label for="email" :value="__('Email Address')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="email"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autocomplete="username"
                            placeholder="you@school.edu" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="password"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Min. 8 characters" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="password_confirmation"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repeat your password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                    </div>

                    <x-primary-button class="w-full justify-center py-3 rounded-lg bg-school-800 hover:bg-school-700 text-sm font-semibold">
                        {{ __('Create Account') }}
                    </x-primary-button>
                </form>

                <p class="text-center text-sm text-slate-500 mt-6">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-school-700 font-medium hover:text-school-600 transition-colors">
                        Sign in
                    </a>
                </p>

            </div>
        </div>
    </div>
</x-guest-layout>