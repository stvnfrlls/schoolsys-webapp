<?php

namespace Tests\Feature\Curriculum;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\GradeLevel;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    // ── Bootstrap ─────────────────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['view attendance', 'create attendance', 'edit attendance', 'delete attendance'] as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    // ── User / role factories ─────────────────────────────────────────────────

    /**
     * @return array{0: User, 1: Faculty}
     */
    private function makeFacultyUser(): array
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'Faculty', 'guard_name' => 'web']);
        $role->syncPermissions(['view attendance', 'create attendance', 'edit attendance']);
        $user->assignRole($role);
        $faculty = Faculty::factory()->create(['user_id' => $user->id]);

        return [$user, $faculty];
    }

    /**
     * @return array{0: User, 1: Student}
     */
    private function makeStudentUser(): array
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'Student', 'guard_name' => 'web']);
        $role->syncPermissions(['view attendance']);
        $user->assignRole($role);

        // Mirrors createStudent() in StudentControllerTest — provides all NOT NULL fields.
        $student = Student::create([
            'user_id'        => $user->id,
            'student_number' => 'STU-' . uniqid(),
            'first_name'     => 'Test',
            'last_name'      => 'Student',
            'status'         => 'active',
        ]);

        return [$user, $student];
    }

    // ── Schedule / data setup ─────────────────────────────────────────────────

    /**
     * Builds a complete schedule setup with enrolled students.
     *
     * Pass a $schoolYear to share one across multiple setups (e.g. cross-faculty
     * summary tests). Otherwise a fresh active school year is created.
     *
     * @return array{schoolYear: SchoolYear, section: Section, schedule: Schedule, students: \Illuminate\Support\Collection}
     */
    private function makeScheduleSetup(
        Faculty $faculty,
        int $studentCount = 2,
        int $dayOfWeek = 1,
        ?SchoolYear $schoolYear = null,
    ): array {
        $schoolYear ??= SchoolYear::factory()->create(['is_active' => 'active']);
        $gradeLevel   = GradeLevel::factory()->create();
        $section      = Section::factory()->create([
            'grade_level_id' => $gradeLevel->id,
            'is_active'      => 'active',
        ]);
        $subject  = Subject::factory()->create();
        $schedule = Schedule::factory()->create([
            'faculty_id'     => $faculty->id,
            'section_id'     => $section->id,
            'school_year_id' => $schoolYear->id,
            'subject_id'     => $subject->id,
            'day_of_week'    => $dayOfWeek,
        ]);

        // Student::factory() omits NOT NULL columns (student_number, first_name,
        // last_name). Create students explicitly so PostgreSQL does not reject them.
        $students = collect(range(1, $studentCount))->map(fn() => Student::create([
            'student_number' => 'STU-' . uniqid(),
            'first_name'     => 'Test',
            'last_name'      => 'Student',
            'status'         => 'active',
        ]));

        // Enrollment has no factory — create directly with all required fields.
        foreach ($students as $student) {
            Enrollment::create([
                'student_id'     => $student->id,
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'enrolled',
                'enrolled_at'    => now(),
            ]);
        }

        return compact('schoolYear', 'section', 'schedule', 'students');
    }

    /** Builds a valid store payload for all enrolled students. */
    private function storePayload(Schedule $schedule, iterable $students, string $date, string $status = 'present'): array
    {
        $records = [];
        $remarks = [];

        foreach ($students as $student) {
            $records[$student->id] = $status;
            $remarks[$student->id] = null;
        }

        return [
            'schedule_id' => $schedule->id,
            'date'        => $date,
            'records'     => $records,
            'remarks'     => $remarks,
        ];
    }

    /** Creates a single Attendance row for convenience. */
    private function makeAttendance(Schedule $schedule, Student $student, string $status = 'present', string $date = '2025-01-06'): Attendance
    {
        // Attendance has no factory — create directly with all required fields.
        return Attendance::create([
            'schedule_id' => $schedule->id,
            'student_id'  => $student->id,
            'date'        => $date,
            'status'      => $status,
        ]);
    }

    // =========================================================================
    // index
    // =========================================================================

    #[Test]
    public function admin_can_view_attendance_index(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('attendance.index'))
            ->assertOk();
    }

    #[Test]
    public function faculty_can_view_attendance_index(): void
    {
        [$user] = $this->makeFacultyUser();

        $this->actingAs($user)
            ->get(route('attendance.index'))
            ->assertOk();
    }

    #[Test]
    public function guest_is_redirected_from_index(): void
    {
        $this->get(route('attendance.index'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function user_without_view_permission_is_forbidden_on_index(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('attendance.index'))
            ->assertForbidden();
    }

    // =========================================================================
    // take
    // =========================================================================

    #[Test]
    public function admin_can_view_take_form_without_a_schedule(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('attendance.take'))
            ->assertOk()
            ->assertViewIs('attendance.take')
            ->assertViewHas('schedule', null)
            ->assertViewHas('students', fn($s) => $s->isEmpty());
    }

    #[Test]
    public function take_loads_students_when_schedule_id_is_provided(): void
    {
        [$user, $faculty] = $this->makeFacultyUser();
        $setup = $this->makeScheduleSetup($faculty, studentCount: 3);

        $this->actingAs($user)
            ->get(route('attendance.take', ['schedule_id' => $setup['schedule']->id]))
            ->assertOk()
            ->assertViewIs('attendance.take')
            ->assertViewHas('students', fn($s) => $s->count() === 3)
            ->assertViewHas('schedule', fn($s) => $s->id === $setup['schedule']->id);
    }

    #[Test]
    public function faculty_cannot_take_attendance_for_another_facultys_schedule(): void
    {
        [$user]           = $this->makeFacultyUser();
        [, $otherFaculty] = $this->makeFacultyUser();
        $setup            = $this->makeScheduleSetup($otherFaculty);

        $this->actingAs($user)
            ->get(route('attendance.take', ['schedule_id' => $setup['schedule']->id]))
            ->assertNotFound();
    }

    #[Test]
    public function take_pre_fills_existing_attendance_for_selected_date(): void
    {
        [$user, $faculty] = $this->makeFacultyUser();
        $setup   = $this->makeScheduleSetup($faculty, studentCount: 2);
        $student = $setup['students']->first();
        $date    = '2025-01-06';

        $this->makeAttendance($setup['schedule'], $student, 'absent', $date);

        $this->actingAs($user)
            ->get(route('attendance.take', ['schedule_id' => $setup['schedule']->id, 'date' => $date]))
            ->assertOk()
            ->assertViewHas('existing', fn($e) => $e->has($student->id));
    }

    // =========================================================================
    // store
    // =========================================================================

    #[Test]
    public function admin_can_store_attendance_records(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 2);
        $date        = '2025-01-06';

        $this->actingAs($this->adminUser)
            ->post(route('attendance.store'), $this->storePayload($setup['schedule'], $setup['students'], $date))
            ->assertRedirect(route('attendance.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('attendances', 2);
        $this->assertDatabaseHas('attendances', [
            'schedule_id' => $setup['schedule']->id,
            'student_id'  => $setup['students']->first()->id,
            'date'        => $date,
            'status'      => 'present',
        ]);
    }

    #[Test]
    public function faculty_can_store_attendance_for_any_valid_schedule(): void
    {
        // Note: store() has no faculty ownership check — any user with
        // 'create attendance' can POST to any schedule_id. This test
        // documents that current behavior.
        [$user, $faculty] = $this->makeFacultyUser();
        $setup            = $this->makeScheduleSetup($faculty, studentCount: 2);

        $this->actingAs($user)
            ->post(route('attendance.store'), $this->storePayload($setup['schedule'], $setup['students'], '2025-01-06'))
            ->assertRedirect(route('attendance.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('attendances', 2);
    }

    #[Test]
    public function store_upserts_existing_records_instead_of_duplicating(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $student     = $setup['students']->first();
        $date        = '2025-01-06';

        // Pre-existing record
        $this->makeAttendance($setup['schedule'], $student, 'present', $date);

        $this->actingAs($this->adminUser)
            ->post(route('attendance.store'), [
                'schedule_id' => $setup['schedule']->id,
                'date'        => $date,
                'records'     => [$student->id => 'absent'],
            ])
            ->assertRedirect(route('attendance.index'));

        // Row count must stay at 1 — no duplicate created
        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', [
            'schedule_id' => $setup['schedule']->id,
            'student_id'  => $student->id,
            'date'        => $date,
            'status'      => 'absent',
        ]);
    }

    #[Test]
    public function store_rejects_non_enrolled_student_ids(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);

        // NOT enrolled in this section — created directly with required fields
        $outsider = Student::create([
            'student_number' => 'STU-' . uniqid(),
            'first_name'     => 'Outside',
            'last_name'      => 'Student',
            'status'         => 'active',
        ]);

        $this->actingAs($this->adminUser)
            ->post(route('attendance.store'), [
                'schedule_id' => $setup['schedule']->id,
                'date'        => '2025-01-06',
                'records'     => [$outsider->id => 'present'],
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseEmpty('attendances');
    }

    #[Test]
    public function store_requires_schedule_id(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('attendance.store'), [
                'date'    => '2025-01-06',
                'records' => [],
            ])
            ->assertSessionHasErrors('schedule_id');
    }

    #[Test]
    public function store_requires_records(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty);

        $this->actingAs($this->adminUser)
            ->post(route('attendance.store'), [
                'schedule_id' => $setup['schedule']->id,
                'date'        => '2025-01-06',
                // records missing
            ])
            ->assertSessionHasErrors('records');
    }

    #[Test]
    public function store_rejects_invalid_status_value(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $student     = $setup['students']->first();

        $this->actingAs($this->adminUser)
            ->post(route('attendance.store'), [
                'schedule_id' => $setup['schedule']->id,
                'date'        => '2025-01-06',
                'records'     => [$student->id => 'tardy'], // not in allowlist
            ])
            ->assertSessionHasErrors('records.*');
    }

    #[Test]
    public function store_is_forbidden_without_create_permission(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);

        $this->actingAs(User::factory()->create()) // no permissions
            ->post(route('attendance.store'), $this->storePayload($setup['schedule'], $setup['students'], '2025-01-06'))
            ->assertForbidden();
    }

    // =========================================================================
    // edit
    // =========================================================================

    #[Test]
    public function admin_can_view_edit_form(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $attendance  = $this->makeAttendance($setup['schedule'], $setup['students']->first());

        $this->actingAs($this->adminUser)
            ->get(route('attendance.edit', $attendance))
            ->assertOk()
            ->assertViewIs('attendance.edit')
            ->assertViewHas('attendance', fn($a) => $a->id === $attendance->id);
    }

    #[Test]
    public function user_without_edit_permission_is_forbidden_on_edit(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $attendance  = $this->makeAttendance($setup['schedule'], $setup['students']->first());

        $this->actingAs(User::factory()->create())
            ->get(route('attendance.edit', $attendance))
            ->assertForbidden();
    }

    // =========================================================================
    // update
    // =========================================================================

    #[Test]
    public function admin_can_update_an_attendance_record(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $attendance  = $this->makeAttendance($setup['schedule'], $setup['students']->first(), 'present');

        $this->actingAs($this->adminUser)
            ->put(route('attendance.update', $attendance), [
                'status'  => 'absent',
                'remarks' => 'Sick leave',
            ])
            ->assertRedirect(route('attendance.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'id'      => $attendance->id,
            'status'  => 'absent',
            'remarks' => 'Sick leave',
        ]);
    }

    #[Test]
    public function update_rejects_invalid_status(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $attendance  = $this->makeAttendance($setup['schedule'], $setup['students']->first());

        $this->actingAs($this->adminUser)
            ->put(route('attendance.update', $attendance), ['status' => 'tardy'])
            ->assertSessionHasErrors('status');
    }

    #[Test]
    public function update_rejects_remarks_exceeding_500_characters(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $attendance  = $this->makeAttendance($setup['schedule'], $setup['students']->first());

        $this->actingAs($this->adminUser)
            ->put(route('attendance.update', $attendance), [
                'status'  => 'present',
                'remarks' => str_repeat('a', 501),
            ])
            ->assertSessionHasErrors('remarks');
    }

    #[Test]
    public function user_without_edit_permission_is_forbidden_on_update(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $attendance  = $this->makeAttendance($setup['schedule'], $setup['students']->first());

        $this->actingAs(User::factory()->create())
            ->put(route('attendance.update', $attendance), ['status' => 'present'])
            ->assertForbidden();
    }

    // =========================================================================
    // destroy
    // =========================================================================

    #[Test]
    public function admin_can_delete_an_attendance_record(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $attendance  = $this->makeAttendance($setup['schedule'], $setup['students']->first());

        $this->actingAs($this->adminUser)
            ->delete(route('attendance.destroy', $attendance))
            ->assertRedirect(route('attendance.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
    }

    #[Test]
    public function user_without_delete_permission_is_forbidden_on_destroy(): void
    {
        [$user]      = $this->makeFacultyUser(); // Faculty has no delete permission
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1);
        $attendance  = $this->makeAttendance($setup['schedule'], $setup['students']->first());

        $this->actingAs($user)
            ->delete(route('attendance.destroy', $attendance))
            ->assertForbidden();

        $this->assertDatabaseHas('attendances', ['id' => $attendance->id]);
    }

    // =========================================================================
    // summary
    // =========================================================================

    #[Test]
    public function admin_can_view_summary_with_all_students(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 2);

        foreach ($setup['students'] as $student) {
            $this->makeAttendance($setup['schedule'], $student);
        }

        $this->actingAs($this->adminUser)
            ->get(route('attendance.summary', ['school_year_id' => $setup['schoolYear']->id]))
            ->assertOk()
            ->assertViewIs('attendance.summary')
            ->assertViewHas('summaries', fn($s) => $s->count() === 2);
    }

    #[Test]
    public function faculty_sees_only_their_own_schedules_in_summary(): void
    {
        [$user, $faculty]   = $this->makeFacultyUser();
        [, $otherFaculty]   = $this->makeFacultyUser();

        // Both schedules share the same school year so the school_year_id
        // filter alone cannot explain the scoping — faculty ownership must.
        $schoolYear  = SchoolYear::factory()->create(['is_active' => 'active']);
        $setup       = $this->makeScheduleSetup($faculty, studentCount: 1, schoolYear: $schoolYear);
        $otherSetup  = $this->makeScheduleSetup($otherFaculty, studentCount: 1, schoolYear: $schoolYear);

        $this->makeAttendance($setup['schedule'], $setup['students']->first());
        $this->makeAttendance($otherSetup['schedule'], $otherSetup['students']->first());

        $this->actingAs($user)
            ->get(route('attendance.summary', ['school_year_id' => $schoolYear->id]))
            ->assertOk()
            ->assertViewHas('summaries', fn($s) => $s->count() === 1);
    }

    #[Test]
    public function student_sees_only_their_own_row_in_summary(): void
    {
        [$studentUser, $student] = $this->makeStudentUser();
        [, $faculty]             = $this->makeFacultyUser();

        $schoolYear   = SchoolYear::factory()->create(['is_active' => 'active']);
        $setup        = $this->makeScheduleSetup($faculty, studentCount: 1, schoolYear: $schoolYear);
        $otherStudent = $setup['students']->first();

        $this->makeAttendance($setup['schedule'], $student);
        $this->makeAttendance($setup['schedule'], $otherStudent);

        $this->actingAs($studentUser)
            ->get(route('attendance.summary', ['school_year_id' => $schoolYear->id]))
            ->assertOk()
            ->assertViewHas(
                'summaries',
                fn($s) => $s->count() === 1 && $s->first()['student']->id === $student->id
            );
    }

    #[Test]
    public function summary_is_forbidden_without_view_permission(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('attendance.summary'))
            ->assertForbidden();
    }

    // =========================================================================
    // load-schedules
    // =========================================================================

    #[Test]
    public function load_schedules_returns_json_for_school_year(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, dayOfWeek: 1);

        $this->actingAs($this->adminUser)
            ->getJson(route('attendance.load-schedules', ['school_year_id' => $setup['schoolYear']->id]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $setup['schedule']->id]);
    }

    #[Test]
    public function load_schedules_filters_by_day_of_week_from_date(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $schoolYear  = SchoolYear::factory()->create(['is_active' => 'active']);

        $mondaySetup  = $this->makeScheduleSetup($faculty, dayOfWeek: 1, schoolYear: $schoolYear); // Mon
        $tuesdaySetup = $this->makeScheduleSetup($faculty, dayOfWeek: 2, schoolYear: $schoolYear); // Tue

        $this->actingAs($this->adminUser)
            ->getJson(route('attendance.load-schedules', [
                'school_year_id' => $schoolYear->id,
                'date'           => '2025-01-06', // Monday → isoWeekday = 1
            ]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $mondaySetup['schedule']->id])
            ->assertJsonMissing(['id' => $tuesdaySetup['schedule']->id]);
    }

    #[Test]
    public function load_schedules_returns_empty_array_for_weekend_date(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty, dayOfWeek: 1);

        $this->actingAs($this->adminUser)
            ->getJson(route('attendance.load-schedules', [
                'school_year_id' => $setup['schoolYear']->id,
                'date'           => '2025-01-11', // Saturday → isoWeekday = 6
            ]))
            ->assertOk()
            ->assertExactJson([]);
    }

    #[Test]
    public function faculty_load_schedules_returns_only_their_own(): void
    {
        [$user, $faculty]   = $this->makeFacultyUser();
        [, $otherFaculty]   = $this->makeFacultyUser();

        $schoolYear  = SchoolYear::factory()->create(['is_active' => 'active']);
        $setup       = $this->makeScheduleSetup($faculty, dayOfWeek: 1, schoolYear: $schoolYear);
        $otherSetup  = $this->makeScheduleSetup($otherFaculty, dayOfWeek: 1, schoolYear: $schoolYear);

        $this->actingAs($user)
            ->getJson(route('attendance.load-schedules', ['school_year_id' => $schoolYear->id]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $setup['schedule']->id])
            ->assertJsonMissing(['id' => $otherSetup['schedule']->id]);
    }

    #[Test]
    public function load_schedules_requires_school_year_id(): void
    {
        $this->actingAs($this->adminUser)
            ->getJson(route('attendance.load-schedules'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('school_year_id');
    }

    #[Test]
    public function load_schedules_is_forbidden_without_view_permission(): void
    {
        [, $faculty] = $this->makeFacultyUser();
        $setup       = $this->makeScheduleSetup($faculty);

        $this->actingAs(User::factory()->create())
            ->getJson(route('attendance.load-schedules', ['school_year_id' => $setup['schoolYear']->id]))
            ->assertForbidden();
    }
}
