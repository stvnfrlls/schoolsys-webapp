<x-guest-layout>
    @section('title', 'Forgot Password')

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
                    <h2 class="text-2xl font-bold text-slate-900">Reset your password</h2>
                    <p class="text-sm text-slate-500 mt-1">Enter your email and we'll send you a reset link</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

                {{-- Info Message --}}
                <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200">
                    <p class="text-sm text-center text-blue-900">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-6">
                        <x-input-label for="email" :value="__('Email address')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <x-text-input
                            id="email"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-600 focus:ring-school-600"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="you@school.edu" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Submit -->
                    <x-primary-button class="w-full justify-center py-3 rounded-lg bg-school-800 hover:bg-school-700 text-sm font-semibold mb-4">
                        {{ __('Email Password Reset Link') }}
                    </x-primary-button>

                    <!-- Back to Login -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-sm text-school-700 hover:text-school-600 font-medium transition-colors">
                            Back to sign in
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>