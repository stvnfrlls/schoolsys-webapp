<x-guest-layout>
    @section('title', 'Verify Email')

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
                    <h2 class="text-2xl font-bold text-slate-900">Verify your email</h2>
                    <p class="text-sm text-slate-500 mt-1">Check your inbox to confirm your account</p>
                </div>

                {{-- Info Message --}}
                <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200">
                    <p class="text-sm text-center text-blue-900">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                    </p>
                </div>

                {{-- Success Message --}}
                @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
                    <p class="text-sm text-center text-green-900 font-medium">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </p>
                </div>
                @endif

                <div class="space-y-3">
                    {{-- Resend Verification Email --}}
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-primary-button class="w-full justify-center py-3 rounded-lg bg-school-800 hover:bg-school-700 text-sm font-semibold">
                            {{ __('Resend Verification Email') }}
                        </x-primary-button>
                    </form>

                    {{-- Log Out --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-3 px-4 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 text-sm font-medium transition-colors">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>