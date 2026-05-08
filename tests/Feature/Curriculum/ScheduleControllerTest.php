<?php

namespace Tests\Feature\Curriculum;

use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ScheduleControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $unauthorizedUser;

    private SchoolYear $schoolYear;
    private Section $section;
    private Subject $subject;
    private Faculty $faculty;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = [
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // Unauthorized user with no permissions
        $this->admin            = User::factory()->create()->assignRole('admin');
        $this->unauthorizedUser = User::factory()->create();

        // Shared supporting records
        $this->schoolYear = SchoolYear::factory()->create();
        $this->section    = Section::factory()->create();
        $this->subject    = Subject::factory()->create();
        $this->faculty    = Faculty::factory()->create(['status' => 'active']);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'school_year_id' => $this->schoolYear->id,
            'section_id'     => $this->section->id,
            'subject_id'     => $this->subject->id,
            'faculty_id'     => $this->faculty->id,
            'day_of_week'    => 1,
            'time_start'     => '08:00',
            'time_end'       => '09:00',
            'room'           => 'Room 101',
        ], $overrides);
    }

    private function createSchedule(array $overrides = []): Schedule
    {
        return Schedule::factory()->create(array_merge([
            'school_year_id' => $this->schoolYear->id,
            'section_id'     => $this->section->id,
            'subject_id'     => $this->subject->id,
            'faculty_id'     => $this->faculty->id,
            'day_of_week'    => 1,
            'time_start'     => '08:00',
            'time_end'       => '09:00',
            'room'           => 'Room 101',
        ], $overrides));
    }

    // ─── Index ────────────────────────────────────────────────────

    public function test_authorized_user_can_view_schedule_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('schedules.index'))
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_view_schedule_index(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->get(route('schedules.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_schedule_index(): void
    {
        $this->get(route('schedules.index'))
            ->assertRedirect(route('login'));
    }

    // ─── Create ───────────────────────────────────────────────────

    public function test_authorized_user_can_view_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('schedules.create'))
            ->assertOk()
            ->assertViewHas(['schoolYears', 'sections', 'subjects', 'faculties']);
    }

    public function test_unauthorized_user_cannot_view_create_form(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->get(route('schedules.create'))
            ->assertForbidden();
    }

    // ─── Store ────────────────────────────────────────────────────

    public function test_authorized_user_can_create_a_schedule(): void
    {
        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('schedules', [
            'school_year_id' => $this->schoolYear->id,
            'section_id'     => $this->section->id,
            'subject_id'     => $this->subject->id,
            'faculty_id'     => $this->faculty->id,
            'day_of_week'    => 1,
            'time_start'     => '08:00:00',
            'time_end'       => '09:00:00',
            'room'           => 'Room 101',
        ]);
    }

    public function test_unauthorized_user_cannot_create_a_schedule(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->post(route('schedules.store'), $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('schedules', 0);
    }

    public function test_store_redirects_to_show_on_success(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload());

        $schedule = Schedule::first();

        $response->assertRedirect(route('schedules.show', $schedule));
    }

    public function test_store_fails_validation_when_required_fields_are_missing(): void
    {
        $this->actingAs($this->admin)
            ->post(route('schedules.store'), [])
            ->assertSessionHasErrors([
                'school_year_id',
                'section_id',
                'subject_id',
                'faculty_id',
                'day_of_week',
                'time_start',
                'time_end',
            ]);
    }

    public function test_store_fails_when_time_end_is_before_time_start(): void
    {
        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload([
                'time_start' => '10:00',
                'time_end'   => '09:00',
            ]))
            ->assertSessionHasErrors('time_end');
    }

    public function test_store_fails_when_time_end_equals_time_start(): void
    {
        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload([
                'time_start' => '09:00',
                'time_end'   => '09:00',
            ]))
            ->assertSessionHasErrors('time_end');
    }

    public function test_store_fails_when_day_of_week_is_out_of_range(): void
    {
        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload([
                'day_of_week' => 7,
            ]))
            ->assertSessionHasErrors('day_of_week');
    }

    public function test_store_accepts_room_as_optional(): void
    {
        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload(['room' => null]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('schedules', ['room' => null]);
    }

    // ─── Store: Conflict Detection ────────────────────────────────

    public function test_store_rejects_faculty_schedule_conflict(): void
    {
        // Existing: faculty teaches 08:00–09:00 on Monday
        $this->createSchedule([
            'faculty_id'  => $this->faculty->id,
            'day_of_week' => 1,
            'time_start'  => '08:00',
            'time_end'    => '09:00',
        ]);

        // New: same faculty, overlapping time on same day
        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload([
                'faculty_id'  => $this->faculty->id,
                'section_id'  => Section::factory()->create()->id,
                'day_of_week' => 1,
                'time_start'  => '08:30',
                'time_end'    => '09:30',
            ]))
            ->assertSessionHasErrors('time_start');
    }

    public function test_store_rejects_section_schedule_conflict(): void
    {
        // Existing: section has a class 08:00–09:00 on Monday
        $this->createSchedule([
            'section_id'  => $this->section->id,
            'day_of_week' => 1,
            'time_start'  => '08:00',
            'time_end'    => '09:00',
        ]);

        // New: same section, overlapping time
        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload([
                'section_id'  => $this->section->id,
                'faculty_id'  => Faculty::factory()->create(['status' => 'active'])->id,
                'day_of_week' => 1,
                'time_start'  => '08:30',
                'time_end'    => '09:30',
            ]))
            ->assertSessionHasErrors('time_start');
    }

    public function test_store_rejects_room_conflict(): void
    {
        $this->createSchedule([
            'room'        => 'Room 101',
            'day_of_week' => 1,
            'time_start'  => '08:00',
            'time_end'    => '09:00',
        ]);

        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload([
                'section_id'  => Section::factory()->create()->id,
                'faculty_id'  => Faculty::factory()->create(['status' => 'active'])->id,
                'room'        => 'Room 101',
                'day_of_week' => 1,
                'time_start'  => '08:00',
                'time_end'    => '09:00',
            ]))
            ->assertSessionHasErrors('time_start');
    }

    public function test_store_allows_same_faculty_on_different_day(): void
    {
        $this->createSchedule([
            'faculty_id'  => $this->faculty->id,
            'day_of_week' => 1, // Monday
            'time_start'  => '08:00',
            'time_end'    => '09:00',
        ]);

        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload([
                'faculty_id'  => $this->faculty->id,
                'day_of_week' => 2, // Tuesday — no conflict
                'time_start'  => '08:00',
                'time_end'    => '09:00',
            ]))
            ->assertSessionHasNoErrors();
    }

    public function test_store_allows_same_faculty_with_non_overlapping_time(): void
    {
        $this->createSchedule([
            'faculty_id'  => $this->faculty->id,
            'day_of_week' => 1,
            'time_start'  => '08:00',
            'time_end'    => '09:00',
        ]);

        $this->actingAs($this->admin)
            ->post(route('schedules.store'), $this->validPayload([
                'faculty_id'  => $this->faculty->id,
                'section_id'  => Section::factory()->create()->id,
                'day_of_week' => 1,
                'time_start'  => '09:00', // starts exactly when previous ends — no overlap
                'time_end'    => '10:00',
            ]))
            ->assertSessionHasNoErrors();
    }

    // ─── Show ─────────────────────────────────────────────────────

    public function test_authorized_user_can_view_a_schedule(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->admin)
            ->get(route('schedules.show', $schedule))
            ->assertOk()
            ->assertViewHas('schedule');
    }

    public function test_unauthorized_user_cannot_view_a_schedule(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->unauthorizedUser)
            ->get(route('schedules.show', $schedule))
            ->assertForbidden();
    }

    // ─── Edit ─────────────────────────────────────────────────────

    public function test_authorized_user_can_view_edit_form(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->admin)
            ->get(route('schedules.edit', $schedule))
            ->assertOk()
            ->assertViewHas(['schedule', 'schoolYears', 'sections', 'subjects', 'faculties']);
    }

    public function test_unauthorized_user_cannot_view_edit_form(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->unauthorizedUser)
            ->get(route('schedules.edit', $schedule))
            ->assertForbidden();
    }

    // ─── Update ───────────────────────────────────────────────────

    public function test_authorized_user_can_update_a_schedule(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->admin)
            ->put(route('schedules.update', $schedule), $this->validPayload([
                'room'      => 'Lab B',
                'time_start' => '10:00',
                'time_end'   => '11:00',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('schedules.show', $schedule));

        $this->assertDatabaseHas('schedules', [
            'id'        => $schedule->id,
            'room'      => 'Lab B',
            'time_start' => '10:00:00',
            'time_end'   => '11:00:00',
        ]);
    }

    public function test_unauthorized_user_cannot_update_a_schedule(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->unauthorizedUser)
            ->put(route('schedules.update', $schedule), $this->validPayload([
                'room' => 'Lab B',
            ]))
            ->assertForbidden();
    }

    public function test_update_does_not_conflict_with_itself(): void
    {
        // This is the critical self-exclusion test.
        // Saving a record with the same time it already has should not trigger a conflict.
        $schedule = $this->createSchedule([
            'day_of_week' => 1,
            'time_start'  => '08:00',
            'time_end'    => '09:00',
        ]);

        $this->actingAs($this->admin)
            ->put(route('schedules.update', $schedule), $this->validPayload([
                'day_of_week' => 1,
                'time_start'  => '08:00',
                'time_end'    => '09:00',
                'room'        => 'Updated Room',
            ]))
            ->assertSessionHasNoErrors();
    }

    public function test_update_rejects_conflict_with_another_schedule(): void
    {
        $anotherFaculty = Faculty::factory()->create(['status' => 'active']);

        // Existing block for the same faculty
        $this->createSchedule([
            'faculty_id'  => $anotherFaculty->id,
            'day_of_week' => 2,
            'time_start'  => '10:00',
            'time_end'    => '11:00',
        ]);

        // Schedule we're editing
        $schedule = $this->createSchedule([
            'faculty_id'  => $anotherFaculty->id,
            'day_of_week' => 3,
            'time_start'  => '08:00',
            'time_end'    => '09:00',
        ]);

        // Try to move it to Tuesday 10:00–11:00 — conflicts with first schedule
        $this->actingAs($this->admin)
            ->put(route('schedules.update', $schedule), $this->validPayload([
                'faculty_id'  => $anotherFaculty->id,
                'day_of_week' => 2,
                'time_start'  => '10:00',
                'time_end'    => '11:00',
            ]))
            ->assertSessionHasErrors('time_start');
    }

    // ─── Destroy ──────────────────────────────────────────────────

    public function test_authorized_user_can_delete_a_schedule(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->admin)
            ->delete(route('schedules.destroy', $schedule))
            ->assertRedirect(route('schedules.index'));

        $this->assertSoftDeleted('schedules', ['id' => $schedule->id]);
    }

    public function test_unauthorized_user_cannot_delete_a_schedule(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->unauthorizedUser)
            ->delete(route('schedules.destroy', $schedule))
            ->assertForbidden();

        $this->assertDatabaseHas('schedules', ['id' => $schedule->id, 'deleted_at' => null]);
    }

    public function test_delete_redirects_with_success_message(): void
    {
        $schedule = $this->createSchedule();

        $this->actingAs($this->admin)
            ->delete(route('schedules.destroy', $schedule))
            ->assertRedirect(route('schedules.index'))
            ->assertSessionHas('success');
    }
}
