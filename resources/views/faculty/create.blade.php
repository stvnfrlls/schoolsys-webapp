<x-app-layout>
    @section('page-title', 'Create Faculty')
    @section('breadcrumb', 'Add a new faculty record')

    @if (session('success'))
        <div
            class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">
        <form action="{{ route('faculty.store') }}" method="POST">
            @csrf

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">New Faculty</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Fill in the details below to add a new faculty member</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('faculty.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Create Faculty
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-10">

                {{-- Section: Portal Account --}}
                <div class="pb-4">
                    <div class="pb-4 border-b border-slate-100 mb-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Portal
                            Account</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Login credentials for the faculty portal</p>
                    </div>

                    <div class="mb-4">
                        <label for="employee_number" class="block text-sm font-medium text-slate-700 mb-2">
                            Employee Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="employee_number" name="employee_number"
                            value="{{ old('employee_number') }}" placeholder="e.g. EMP-2024-0001"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('employee_number') border-red-500 @enderror">
                        @error('employee_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="faculty@example.com"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" placeholder="••••••••"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-400">Minimum 8 characters</p>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="••••••••"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600">
                    </div>
                </div>

                {{-- Section: Personal Information --}}
                <div class="pb-4">
                    <div class="py-3 border-b border-slate-100 mb-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Personal
                            Information</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Faculty member's basic personal details</p>
                    </div>

                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-slate-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                placeholder="Juan"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('first_name') border-red-500 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-slate-700 mb-2">
                                Middle Name
                            </label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                placeholder="Santos"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('middle_name') border-red-500 @enderror">
                            @error('middle_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="last_name" class="block text-sm font-medium text-slate-700 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                            placeholder="Dela Cruz"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('last_name') border-red-500 @enderror">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-slate-700 mb-2">
                                Birth Date
                            </label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('birth_date') border-red-500 @enderror">
                            @error('birth_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-slate-700 mb-2">
                                Gender
                            </label>
                            <select id="gender" name="gender"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('gender') border-red-500 @enderror">
                                <option value="">-- Select --</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="contact_number" class="block text-sm font-medium text-slate-700 mb-2">
                            Contact Number
                        </label>
                        <input type="text" id="contact_number" name="contact_number"
                            value="{{ old('contact_number') }}" placeholder="09XX XXX XXXX"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('contact_number') border-red-500 @enderror">
                        @error('contact_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-slate-700 mb-2">
                            Address
                        </label>
                        <textarea id="address" name="address" rows="3"
                            placeholder="Street, Barangay, City, Province"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Section: Academic Information --}}
                <div class="pb-4">
                    <div class="py-3 border-b border-slate-100 mb-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Academic
                            Information</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Department, rank, and area of specialization</p>
                    </div>

                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="department" class="block text-sm font-medium text-slate-700 mb-2">
                                Department
                            </label>
                            <input type="text" id="department" name="department" value="{{ old('department') }}"
                                placeholder="e.g. Mathematics"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('department') border-red-500 @enderror">
                            @error('department')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-slate-700 mb-2">
                                Position
                            </label>
                            <input type="text" id="position" name="position" value="{{ old('position') }}"
                                placeholder="e.g. Department Head"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('position') border-red-500 @enderror">
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="rank" class="block text-sm font-medium text-slate-700 mb-2">
                                Academic Rank
                            </label>
                            <select id="rank" name="rank"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('rank') border-red-500 @enderror">
                                <option value="">-- Select Rank --</option>
                                <option value="instructor" {{ old('rank') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                                <option value="assistant_professor" {{ old('rank') === 'assistant_professor' ? 'selected' : '' }}>Assistant Professor</option>
                                <option value="associate_professor" {{ old('rank') === 'associate_professor' ? 'selected' : '' }}>Associate Professor</option>
                                <option value="professor" {{ old('rank') === 'professor' ? 'selected' : '' }}>Professor</option>
                            </select>
                            @error('rank')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="specialization" class="block text-sm font-medium text-slate-700 mb-2">
                                Specialization
                            </label>
                            <input type="text" id="specialization" name="specialization"
                                value="{{ old('specialization') }}" placeholder="e.g. Calculus, Algebra"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('specialization') border-red-500 @enderror">
                            @error('specialization')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Employment --}}
                <div class="pb-4">
                    <div class="pb-4 border-b border-slate-100 mb-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Employment</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Employment type and current status</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="employment_type" class="block text-sm font-medium text-slate-700 mb-2">
                                Employment Type
                            </label>
                            <select id="employment_type" name="employment_type"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('employment_type') border-red-500 @enderror">
                                <option value="">-- Select Type --</option>
                                <option value="full_time" {{ old('employment_type', 'full_time') === 'full_time' ? 'selected' : '' }}>Full-time</option>
                                <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part-time</option>
                            </select>
                            @error('employment_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('status') border-red-500 @enderror">
                                <option value="">-- Select Status --</option>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="retired" {{ old('status') === 'retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

</x-app-layout>