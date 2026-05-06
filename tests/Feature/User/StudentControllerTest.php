<?php

namespace Tests\Feature\User;

use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = [
            'view students',
            'create students',
            'edit students',
            'delete students',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        Role::firstOrCreate(['name' => 'student']);

        // Create an admin user with all permissions
        $this->adminUser = User::factory()->create([
            'status' => 'active',
        ]);
        $this->adminUser->assignRole('admin');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Returns a valid payload for creating a student.
     */
    private function validPayload(array $overrides = []): array
    {
        $gradeLevel = GradeLevel::create([
            'name'      => 'Grade 1',
            'level'     => 1,
            'is_active' => 'active',
        ]);

        $section = Section::create([
            'grade_level_id' => $gradeLevel->id,
            'name'           => 'Section A',
            'is_active'      => 'active',
        ]);

        $schoolYear = SchoolYear::create([
            'name'       => '2024-2025',
            'start_date' => '2024-06-01',
            'end_date'   => '2025-03-31',
            'is_active'  => 'active',
        ]);

        return array_merge([
            'student_number'        => 'STU-2024-001',
            'first_name'            => 'John',
            'middle_name'           => 'Paul',
            'last_name'             => 'Doe',
            'email'                 => 'john.doe@example.com',
            'password'              => 'Password@123',
            'password_confirmation' => 'Password@123',
            'birth_date'            => '2000-01-15',
            'gender'                => 'male',
            'address'               => '123 Main St',
            'contact_number'        => '09171234567',
            'guardian_name'         => 'Jane Doe',
            'guardian_contact'      => '09179876543',
            'guardian_relationship' => 'Mother',
            'status'                => 'active',
            'school_year_id'        => $schoolYear->id,
            'section_id'            => $section->id,
        ], $overrides);
    }

    /**
     * Creates a student (with linked user) directly in the DB.
     */
    private function createStudent(array $overrides = []): Student
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('student');

        return Student::create(array_merge([
            'user_id'        => $user->id,
            'student_number' => 'STU-' . uniqid(),
            'first_name'     => 'Test',
            'last_name'      => 'Student',
            'status'         => 'active',
        ], $overrides));
    }

    // =========================================================================
    // INDEX
    // =========================================================================

    #[Test]
    public function authenticated_user_with_permission_can_view_student_list(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('students.index'))
            ->assertOk();
    }

    #[Test]
    public function unauthenticated_user_is_redirected_from_student_list(): void
    {
        $this->get(route('students.index'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function user_without_view_permission_cannot_access_student_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('students.index'))
            ->assertForbidden();
    }

    // =========================================================================
    // CREATE
    // =========================================================================

    #[Test]
    public function user_with_create_permission_can_view_create_form(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('students.create'))
            ->assertOk()
            ->assertViewIs('students.create');
    }

    #[Test]
    public function user_without_create_permission_cannot_view_create_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('students.create'))
            ->assertForbidden();
    }

    // =========================================================================
    // STORE
    // =========================================================================

    #[Test]
    public function user_with_permission_can_create_a_student(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('students.store'), $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('students', [
            'student_number' => 'STU-2024-001',
            'first_name'     => 'John',
            'last_name'      => 'Doe',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    #[Test]
    public function creating_a_student_also_creates_an_associated_user(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('students.store'), $this->validPayload());

        $user = User::where('email', 'john.doe@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('student'));
        $this->assertEquals('John Doe', $user->name);
    }

    #[Test]
    public function creating_a_student_hashes_the_password(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('students.store'), $this->validPayload());

        $user = User::where('email', 'john.doe@example.com')->first();

        $this->assertTrue(Hash::check('Password@123', $user->password));
    }

    #[Test]
    public function store_fails_validation_when_required_fields_are_missing(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('students.store'), [])
            ->assertSessionHasErrors([
                'first_name',
                'last_name',
                'email',
                'password',
                'student_number',
            ]);
    }

    #[Test]
    public function store_fails_when_email_is_already_taken(): void
    {
        User::factory()->create(['email' => 'john.doe@example.com']);

        $this->actingAs($this->adminUser)
            ->post(route('students.store'), $this->validPayload())
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function store_fails_when_student_number_is_already_taken(): void
    {
        $this->createStudent(['student_number' => 'STU-2024-001']);

        $this->actingAs($this->adminUser)
            ->post(route('students.store'), $this->validPayload())
            ->assertSessionHasErrors('student_number');
    }

    #[Test]
    public function store_redirects_to_student_show_page_on_success(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('students.store'), $this->validPayload())
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    #[Test]
    public function user_without_create_permission_cannot_store_a_student(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('students.store'), $this->validPayload())
            ->assertForbidden();
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    #[Test]
    public function user_with_permission_can_view_a_student(): void
    {
        $student = $this->createStudent();

        $this->actingAs($this->adminUser)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertViewIs('students.show')
            ->assertViewHas('student', $student);
    }

    #[Test]
    public function user_without_view_permission_cannot_view_a_student(): void
    {
        $user    = User::factory()->create();
        $student = $this->createStudent();

        $this->actingAs($user)
            ->get(route('students.show', $student))
            ->assertForbidden();
    }

    #[Test]
    public function show_returns_404_for_nonexistent_student(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('students.show', 99999))
            ->assertNotFound();
    }

    // =========================================================================
    // EDIT
    // =========================================================================

    #[Test]
    public function user_with_permission_can_view_edit_form(): void
    {
        $student = $this->createStudent();

        $this->actingAs($this->adminUser)
            ->get(route('students.edit', $student))
            ->assertOk()
            ->assertViewIs('students.edit')
            ->assertViewHas('student', $student);
    }

    #[Test]
    public function user_without_edit_permission_cannot_view_edit_form(): void
    {
        $user    = User::factory()->create();
        $student = $this->createStudent();

        $this->actingAs($user)
            ->get(route('students.edit', $student))
            ->assertForbidden();
    }

    // =========================================================================
    // UPDATE
    // =========================================================================

    #[Test]
    public function user_with_permission_can_update_a_student(): void
    {
        $student = $this->createStudent();

        $payload = [
            'student_number' => $student->student_number,
            'first_name'     => 'Jane',
            'last_name'      => 'Smith',
            'email'          => $student->user->email,
            'status'         => 'active',
        ];

        $this->actingAs($this->adminUser)
            ->put(route('students.update', $student), $payload)
            ->assertRedirect(route('students.show', $student))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'id'         => $student->id,
            'first_name' => 'Jane',
            'last_name'  => 'Smith',
        ]);
    }

    #[Test]
    public function updating_a_student_also_updates_the_linked_user_name(): void
    {
        $student = $this->createStudent();

        $this->actingAs($this->adminUser)
            ->put(route('students.update', $student), [
                'student_number' => $student->student_number,
                'first_name'     => 'UpdatedFirst',
                'last_name'      => 'UpdatedLast',
                'email'          => $student->user->email,
                'status'         => 'active',
            ]);

        $this->assertDatabaseHas('users', [
            'id'   => $student->user_id,
            'name' => 'UpdatedFirst UpdatedLast',
        ]);
    }

    #[Test]
    public function update_fails_validation_when_required_fields_are_missing(): void
    {
        $student = $this->createStudent();

        $this->actingAs($this->adminUser)
            ->put(route('students.update', $student), [])
            ->assertSessionHasErrors(['first_name', 'last_name', 'status']);
    }

    #[Test]
    public function user_without_edit_permission_cannot_update_a_student(): void
    {
        $user    = User::factory()->create();
        $student = $this->createStudent();

        $this->actingAs($user)
            ->put(route('students.update', $student), [
                'student_number' => $student->student_number,
                'first_name'     => 'Jane',
                'last_name'      => 'Smith',
                'email'          => $student->user->email,
                'status'         => 'active',
            ])
            ->assertForbidden();
    }

    // =========================================================================
    // DESTROY
    // =========================================================================

    #[Test]
    public function user_with_permission_can_delete_a_student(): void
    {
        $student   = $this->createStudent();
        $studentId = $student->id;
        $userId    = $student->user_id;

        $this->actingAs($this->adminUser)
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('students', ['id' => $studentId]);
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    #[Test]
    public function deleting_a_student_also_deletes_the_linked_user(): void
    {
        $student = $this->createStudent();
        $userId  = $student->user_id;

        $this->actingAs($this->adminUser)
            ->delete(route('students.destroy', $student));

        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    #[Test]
    public function user_without_delete_permission_cannot_delete_a_student(): void
    {
        $user    = User::factory()->create();
        $student = $this->createStudent();

        $this->actingAs($user)
            ->delete(route('students.destroy', $student))
            ->assertForbidden();

        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    #[Test]
    public function destroy_returns_404_for_nonexistent_student(): void
    {
        $this->actingAs($this->adminUser)
            ->delete(route('students.destroy', 99999))
            ->assertNotFound();
    }
}
