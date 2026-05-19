<?php

namespace Tests\Feature\Curriculum;

use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EnrollmentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $permissions = [
            'view enrollments',
            'create enrollments',
            'edit enrollments',
            'delete enrollments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->user->givePermissionTo($permissions);
    }

    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------

    public function test_index_renders_successfully(): void
    {
        $this->actingAs($this->user)
            ->get(route('enrollments.index'))
            ->assertOk();
    }

    public function test_unauthenticated_user_cannot_access_index(): void
    {
        $this->get(route('enrollments.index'))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function test_create_page_renders_successfully(): void
    {
        $this->actingAs($this->user)
            ->get(route('enrollments.create'))
            ->assertOk()
            ->assertViewIs('enrollments.create');
    }

    // -------------------------------------------------------------------------
    // STORE
    // -------------------------------------------------------------------------

    public function test_store_creates_enrollment_and_redirects(): void
    {
        $student    = Student::factory()->create();
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $payload = [
            'student_id'     => $student->id,
            'section_id'     => $section->id,
            'school_year_id' => $schoolYear->id,
            'status'         => 'enrolled',
            'enrolled_at'    => now()->toDateString(),
        ];

        $this->actingAs($this->user)
            ->post(route('enrollments.store'), $payload)
            ->assertSessionHas('success');

        $enrollment = Enrollment::where('student_id', $student->id)->firstOrFail();

        $this->assertDatabaseHas('enrollments', [
            'student_id'     => $student->id,
            'section_id'     => $section->id,
            'school_year_id' => $schoolYear->id,
            'status'         => 'enrolled',
        ]);

        // Confirm the show page is reachable (redirect target exists)
        $this->actingAs($this->user)
            ->get(route('enrollments.show', $enrollment))
            ->assertOk();
    }

    public function test_store_fails_when_student_id_is_missing(): void
    {
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->post(route('enrollments.store'), [
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'enrolled',
                'enrolled_at'    => now()->toDateString(),
            ])
            ->assertSessionHasErrors('student_id');
    }

    public function test_store_fails_when_section_id_is_missing(): void
    {
        $student    = Student::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->post(route('enrollments.store'), [
                'student_id'     => $student->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'enrolled',
                'enrolled_at'    => now()->toDateString(),
            ])
            ->assertSessionHasErrors('section_id');
    }

    public function test_store_fails_when_school_year_id_is_missing(): void
    {
        $student = Student::factory()->create();
        $section = Section::factory()->create();

        $this->actingAs($this->user)
            ->post(route('enrollments.store'), [
                'student_id'  => $student->id,
                'section_id'  => $section->id,
                'status'      => 'enrolled',
                'enrolled_at' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('school_year_id');
    }

    public function test_store_fails_when_status_is_invalid(): void
    {
        $student    = Student::factory()->create();
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->post(route('enrollments.store'), [
                'student_id'     => $student->id,
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'unknown_status',
                'enrolled_at'    => now()->toDateString(),
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_store_fails_when_enrolled_at_is_not_a_date(): void
    {
        $student    = Student::factory()->create();
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->post(route('enrollments.store'), [
                'student_id'     => $student->id,
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'enrolled',
                'enrolled_at'    => 'not-a-date',
            ])
            ->assertSessionHasErrors('enrolled_at');
    }

    public function test_store_fails_when_student_does_not_exist(): void
    {
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->post(route('enrollments.store'), [
                'student_id'     => 99999,
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'enrolled',
                'enrolled_at'    => now()->toDateString(),
            ])
            ->assertSessionHasErrors('student_id');
    }

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------

    public function test_show_renders_successfully(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($this->user)
            ->get(route('enrollments.show', $enrollment))
            ->assertOk()
            ->assertViewIs('enrollments.show')
            ->assertViewHas('enrollment', $enrollment);
    }

    public function test_show_returns_404_for_nonexistent_enrollment(): void
    {
        $this->actingAs($this->user)
            ->get(route('enrollments.show', 999))
            ->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------

    public function test_edit_page_renders_successfully(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($this->user)
            ->get(route('enrollments.edit', $enrollment))
            ->assertOk()
            ->assertViewIs('enrollments.edit')
            ->assertViewHas('enrollment', $enrollment);
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    public function test_update_modifies_enrollment_and_redirects(): void
    {
        $student    = Student::factory()->create();
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $enrollment = Enrollment::factory()->create([
            'student_id'     => $student->id,
            'section_id'     => $section->id,
            'school_year_id' => $schoolYear->id,
            'status'         => 'enrolled',
            'enrolled_at'    => now()->toDateString(),
        ]);

        $newStudent    = Student::factory()->create();
        $newSection    = Section::factory()->create();
        $newSchoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->put(route('enrollments.update', $enrollment), [
                'student_id'     => $newStudent->id,
                'section_id'     => $newSection->id,
                'school_year_id' => $newSchoolYear->id,
                'status'         => 'dropped',
                'enrolled_at'    => now()->toDateString(),
            ])
            ->assertRedirect(route('enrollments.show', $enrollment))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'id'             => $enrollment->id,
            'student_id'     => $newStudent->id,
            'section_id'     => $newSection->id,
            'school_year_id' => $newSchoolYear->id,
            'status'         => 'dropped',
        ]);
    }

    public function test_update_fails_when_student_id_is_missing(): void
    {
        $enrollment = Enrollment::factory()->create();
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->put(route('enrollments.update', $enrollment), [
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'enrolled',
                'enrolled_at'    => now()->toDateString(),
            ])
            ->assertSessionHasErrors('student_id');
    }

    public function test_update_fails_when_status_is_invalid(): void
    {
        $enrollment = Enrollment::factory()->create();
        $student    = Student::factory()->create();
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->put(route('enrollments.update', $enrollment), [
                'student_id'     => $student->id,
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'unknown_status',
                'enrolled_at'    => now()->toDateString(),
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_update_fails_when_enrolled_at_is_not_a_date(): void
    {
        $enrollment = Enrollment::factory()->create();
        $student    = Student::factory()->create();
        $section    = Section::factory()->create();
        $schoolYear = SchoolYear::factory()->create();

        $this->actingAs($this->user)
            ->put(route('enrollments.update', $enrollment), [
                'student_id'     => $student->id,
                'section_id'     => $section->id,
                'school_year_id' => $schoolYear->id,
                'status'         => 'enrolled',
                'enrolled_at'    => 'not-a-date',
            ])
            ->assertSessionHasErrors('enrolled_at');
    }

    // -------------------------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------------------------

    public function test_destroy_deletes_enrollment_and_redirects(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('enrollments.destroy', $enrollment))
            ->assertRedirect(route('enrollments.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted($enrollment);
    }

    public function test_destroy_returns_404_for_nonexistent_enrollment(): void
    {
        $this->actingAs($this->user)
            ->delete(route('enrollments.destroy', 999))
            ->assertNotFound();
    }
}
