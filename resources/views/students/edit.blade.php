<x-app-layout>
    @section('page-title', 'Edit Student')
    @section('breadcrumb', 'Update student record')

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
        <form action="{{ route('students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800 font-sans">Edit Student</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Update the details for {{ $student->first_name }}
                            {{ $student->last_name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('students.show', $student) }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                            Save Changes
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
                        <p class="text-xs text-slate-400 mt-0.5">Login credentials for the student portal</p>
                    </div>

                    <div class="mb-4">
                        <label for="student_number" class="block text-sm font-medium text-slate-700 mb-2">
                            Student Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="student_number" name="student_number"
                            value="{{ old('student_number', $student->student_number) }}" placeholder="e.g. 2024-0001"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('student_number') border-red-500 @enderror">
                        @error('student_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}"
                            placeholder="student@example.com"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                            New Password
                        </label>
                        <input type="password" id="password" name="password" placeholder="••••••••"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-400">Leave blank to keep the current password. Minimum 8
                            characters.</p>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                            Confirm New Password
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
                        <p class="text-xs text-slate-400 mt-0.5">Student's basic personal details</p>
                    </div>

                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-slate-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name"
                                value="{{ old('first_name', $student->first_name) }}" placeholder="Juan"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('first_name') border-red-500 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-slate-700 mb-2">
                                Middle Name
                            </label>
                            <input type="text" id="middle_name" name="middle_name"
                                value="{{ old('middle_name', $student->middle_name) }}" placeholder="Santos"
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
                        <input type="text" id="last_name" name="last_name"
                            value="{{ old('last_name', $student->last_name) }}" placeholder="Dela Cruz"
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
                            <input type="date" id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}"
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
                                <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>
                                    Male</option>
                                <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
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
                            value="{{ old('contact_number', $student->contact_number) }}" placeholder="09XX XXX XXXX"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('contact_number') border-red-500 @enderror">
                        @error('contact_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-slate-700 mb-2">
                            Address
                        </label>
                        <textarea id="address" name="address" rows="3" placeholder="Street, Barangay, City, Province"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('address') border-red-500 @enderror">{{ old('address', $student->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Section: Guardian Information --}}
                <div class="pb-4">
                    <div class="py-3 border-b border-slate-100 mb-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Guardian
                            Information</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Parent or guardian contact details</p>
                    </div>

                    <div class="mb-4">
                        <label for="guardian_name" class="block text-sm font-medium text-slate-700 mb-2">
                            Guardian Name
                        </label>
                        <input type="text" id="guardian_name" name="guardian_name"
                            value="{{ old('guardian_name', $student->guardian_name) }}" placeholder="Maria Dela Cruz"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('guardian_name') border-red-500 @enderror">
                        @error('guardian_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="guardian_contact" class="block text-sm font-medium text-slate-700 mb-2">
                                Guardian Contact
                            </label>
                            <input type="text" id="guardian_contact" name="guardian_contact"
                                value="{{ old('guardian_contact', $student->guardian_contact) }}"
                                placeholder="09XX XXX XXXX"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('guardian_contact') border-red-500 @enderror">
                            @error('guardian_contact')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guardian_relationship" class="block text-sm font-medium text-slate-700 mb-2">
                                Relationship
                            </label>
                            <input type="text" id="guardian_relationship" name="guardian_relationship"
                                value="{{ old('guardian_relationship', $student->guardian_relationship) }}"
                                placeholder="Mother, Father, Sibling..."
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('guardian_relationship') border-red-500 @enderror">
                            @error('guardian_relationship')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Enrollment Status --}}
                <div class="pb-4">
                    <div class="py-3 border-b border-slate-100 mb-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest font-sans">Enrollment
                            Status</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Current academic standing of the student</p>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('status') border-red-500 @enderror">
                            <option value="">-- Select Status --</option>
                            <option value="enrolled" {{ old('status', $student->status) === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                            <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="dropped" {{ old('status', $student->status) === 'dropped' ? 'selected' : '' }}>
                                Dropped</option>
                            <option value="transferee" {{ old('status', $student->status) === 'transferee' ? 'selected' : '' }}>Transferee</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>
        </form>
    </div>

</x-app-layout>