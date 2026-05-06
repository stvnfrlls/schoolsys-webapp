<x-app-layout>
    @section('page-title', 'Edit Enrollment')
    @section('breadcrumb', 'Update enrollment details')

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 font-sans">Edit Enrollment</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Update enrollment details</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('enrollments.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button form="edit-enrollment-form" type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-school-800 text-white hover:bg-school-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form id="edit-enrollment-form" action="{{ route('enrollments.update', $enrollment) }}" method="POST"
            class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Student --}}
            <div>
                <label for="student_id" class="block text-sm font-medium text-slate-700 mb-2">
                    Student <span class="text-red-500">*</span>
                </label>
                <select id="student_id" name="student_id"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('student_id') border-red-500 @enderror">
                    <option value="">-- Select Student --</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}"
                            {{ old('student_id', $enrollment->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section --}}
            <div>
                <label for="section_id" class="block text-sm font-medium text-slate-700 mb-2">
                    Section <span class="text-red-500">*</span>
                </label>
                <select id="section_id" name="section_id"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('section_id') border-red-500 @enderror">
                    <option value="">-- Select Section --</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}"
                            {{ old('section_id', $enrollment->section_id) == $section->id ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endforeach
                </select>
                @error('section_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- School Year --}}
            <div>
                <label for="school_year_id" class="block text-sm font-medium text-slate-700 mb-2">
                    School Year <span class="text-red-500">*</span>
                </label>
                <select id="school_year_id" name="school_year_id"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('school_year_id') border-red-500 @enderror">
                    <option value="">-- Select School Year --</option>
                    @foreach ($schoolYears as $schoolyear)
                        <option value="{{ $schoolyear->id }}"
                            {{ old('school_year_id', $enrollment->school_year_id) == $schoolyear->id ? 'selected' : '' }}>
                            {{ $schoolyear->name }}
                        </option>
                    @endforeach
                </select>
                @error('school_year_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Enrolled At --}}
            <div>
                <label for="enrolled_at" class="block text-sm font-medium text-slate-700 mb-2">
                    Enrollment Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="enrolled_at" name="enrolled_at"
                    value="{{ old('enrolled_at', \Carbon\Carbon::parse($enrollment->enrolled_at)->toDateString()) }}"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('enrolled_at') border-red-500 @enderror">
                @error('enrolled_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                    class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-school-600 focus:border-school-600 @error('status') border-red-500 @enderror">
                    <option value="enrolled"    {{ old('status', $enrollment->status) === 'enrolled'    ? 'selected' : '' }}>Enrolled</option>
                    <option value="dropped"     {{ old('status', $enrollment->status) === 'dropped'     ? 'selected' : '' }}>Dropped</option>
                    <option value="transferred" {{ old('status', $enrollment->status) === 'transferred' ? 'selected' : '' }}>Transferred</option>
                    <option value="completed"   {{ old('status', $enrollment->status) === 'completed'   ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Changing status to Dropped or Transferred will affect reporting</p>
            </div>

            {{-- Record info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Created</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $enrollment->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $enrollment->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Last Updated</p>
                    <p class="text-sm text-slate-700 font-medium">{{ $enrollment->updated_at->format('M d, Y H:i') }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $enrollment->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('delete-form').submit()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                    Delete Enrollment
                </button>
            </div>

        </form>

        {{-- Hidden delete form --}}
        <form id="delete-form" action="{{ route('enrollments.destroy', $enrollment) }}" method="POST"
            onsubmit="return confirm('Are you sure? This action cannot be undone.');">
            @csrf
            @method('DELETE')
        </form>

    </div>

</x-app-layout>