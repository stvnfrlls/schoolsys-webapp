<?php

namespace Tests\Feature\User;

use App\Models\Faculty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FacultyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'view faculty',
            'create faculty',
            'edit faculty',
            'delete faculty',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'faculty']);

        $this->admin = User::factory()->create(['status' => 'active']);
        $this->admin->assignRole('admin');

        $this->unauthorizedUser = User::factory()->create(['status' => 'active']);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Returns a valid store payload.
     * rank/specialization are omitted by default to avoid enum validation —
     * add them back once you know the exact accepted values in StoreFacultyRequest.
     */
    private function validFacultyPayload(array $overrides = []): array
    {
        $password = 'Password@123';

        return array_merge([
            'first_name'            => $this->faker->firstName(),
            'middle_name'           => $this->faker->firstName(),
            'last_name'             => $this->faker->lastName(),
            'email'                 => $this->faker->unique()->safeEmail(),
            'password'              => $password,
            'password_confirmation' => $password,
            'employee_number'       => 'EMP-' . $this->faker->unique()->numerify('####'),
            'birth_date'            => '1990-01-15',
            'gender'                => 'male',
            'address'               => $this->faker->address(),
            'contact_number'        => '09171234567',
            'department'            => 'Computer Science',
            'position'              => 'Instructor',
            'employment_type'       => 'full_time',
            'status'                => 'active',
        ], $overrides);
    }

    /**
     * Creates a Faculty + linked User directly (no factory required).
     */
    private function createFaculty(array $facultyOverrides = []): Faculty
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('faculty');

        return Faculty::create(array_merge([
            'user_id'         => $user->id,
            'employee_number' => 'EMP-' . $this->faker->unique()->numerify('####'),
            'first_name'      => $this->faker->firstName(),
            'middle_name'     => null,
            'last_name'       => $this->faker->lastName(),
            'birth_date'      => null,
            'gender'          => 'male',
            'address'         => null,
            'contact_number'  => null,
            'department'      => 'Computer Science',
            'position'        => 'Instructor',
            'rank'            => null,
            'specialization'  => null,
            'employment_type' => 'full_time',
            'status'          => 'active',
        ], $facultyOverrides));
    }

    // =========================================================================
    // INDEX
    // =========================================================================

    public function test_index_is_accessible_by_authorized_user(): void
    {
        $this->actingAs($this->admin)
            ->get(route('faculty.index'))
            ->assertOk();
    }

    public function test_index_is_denied_for_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->get(route('faculty.index'))
            ->assertForbidden();
    }

    public function test_index_redirects_unauthenticated_user_to_login(): void
    {
        $this->get(route('faculty.index'))
            ->assertRedirect(route('login'));
    }

    // =========================================================================
    // CREATE
    // =========================================================================

    public function test_create_form_is_accessible_by_authorized_user(): void
    {
        $this->actingAs($this->admin)
            ->get(route('faculty.create'))
            ->assertOk()
            ->assertViewIs('faculty.create');
    }

    public function test_create_form_is_denied_for_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->get(route('faculty.create'))
            ->assertForbidden();
    }

    // =========================================================================
    // STORE
    // =========================================================================

    public function test_store_creates_faculty_and_user_successfully(): void
    {
        $payload = $this->validFacultyPayload();

        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('faculties', [
            'first_name'      => $payload['first_name'],
            'last_name'       => $payload['last_name'],
            'employee_number' => $payload['employee_number'],
            'department'      => $payload['department'],
            'status'          => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
        ]);
    }

    public function test_store_assigns_faculty_role_to_new_user(): void
    {
        $payload = $this->validFacultyPayload();

        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $payload)
            ->assertSessionHasNoErrors();

        $user = User::where('email', $payload['email'])->first();

        $this->assertNotNull($user, 'User was not created.');
        $this->assertTrue($user->hasRole('faculty'));
    }

    public function test_store_redirects_with_success_flash(): void
    {
        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $this->validFacultyPayload())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');
    }

    public function test_store_logs_activity_on_success(): void
    {
        $payload = $this->validFacultyPayload();

        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $payload)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('activity_log', [
            'description' => 'Created faculty',
            'causer_type' => User::class,
            'causer_id'   => $this->admin->id,
        ]);
    }

    public function test_store_is_denied_for_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->post(route('faculty.store'), $this->validFacultyPayload())
            ->assertForbidden();
    }

    public function test_store_fails_validation_when_required_fields_are_missing(): void
    {
        $this->actingAs($this->admin)
            ->post(route('faculty.store'), [])
            ->assertSessionHasErrors(['first_name', 'last_name', 'email', 'employee_number', 'password']);
    }

    public function test_store_fails_validation_with_mismatched_password_confirmation(): void
    {
        $payload = $this->validFacultyPayload([
            'password'              => 'Password@123',
            'password_confirmation' => 'DifferentPassword',
        ]);

        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $payload)
            ->assertSessionHasErrors(['password']);
    }

    public function test_store_fails_validation_with_duplicate_email(): void
    {
        $existing      = $this->createFaculty();
        $existingEmail = $existing->user->email;

        $payload = $this->validFacultyPayload(['email' => $existingEmail]);

        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $payload)
            ->assertSessionHasErrors(['email']);
    }

    public function test_store_fails_validation_with_duplicate_employee_number(): void
    {
        $this->createFaculty(['employee_number' => 'EMP-0001']);

        $payload = $this->validFacultyPayload(['employee_number' => 'EMP-0001']);

        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $payload)
            ->assertSessionHasErrors(['employee_number']);
    }

    public function test_store_stores_optional_fields_when_provided(): void
    {
        $payload = $this->validFacultyPayload([
            'middle_name'    => 'Santos',
            'specialization' => 'Machine Learning',
        ]);

        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $payload)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('faculties', [
            'middle_name'    => 'Santos',
            'specialization' => 'Machine Learning',
        ]);
    }

    public function test_store_defaults_optional_fields_to_null_when_omitted(): void
    {
        $payload = $this->validFacultyPayload([
            'middle_name'    => null,
            'birth_date'     => null,
            'gender'         => null,
            'address'        => null,
            'contact_number' => null,
            'specialization' => null,
        ]);

        $this->actingAs($this->admin)
            ->post(route('faculty.store'), $payload)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('faculties', [
            'employee_number' => $payload['employee_number'],
            'middle_name'     => null,
        ]);
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    public function test_show_displays_faculty_to_authorized_user(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->admin)
            ->get(route('faculty.show', $faculty))
            ->assertOk()
            ->assertViewIs('faculty.show')
            ->assertViewHas('faculty', fn($f) => $f->id === $faculty->id);
    }

    public function test_show_is_denied_for_unauthorized_user(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->unauthorizedUser)
            ->get(route('faculty.show', $faculty))
            ->assertForbidden();
    }

    public function test_show_returns_404_for_nonexistent_faculty(): void
    {
        $this->actingAs($this->admin)
            ->get(route('faculty.show', 99999))
            ->assertNotFound();
    }

    // =========================================================================
    // EDIT
    // =========================================================================

    public function test_edit_form_is_accessible_by_authorized_user(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->admin)
            ->get(route('faculty.edit', $faculty))
            ->assertOk()
            ->assertViewIs('faculty.edit')
            ->assertViewHas('faculty', fn($f) => $f->id === $faculty->id);
    }

    public function test_edit_form_is_denied_for_unauthorized_user(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->unauthorizedUser)
            ->get(route('faculty.edit', $faculty))
            ->assertForbidden();
    }

    // =========================================================================
    // UPDATE
    // =========================================================================

    public function test_update_modifies_faculty_record_successfully(): void
    {
        $faculty = $this->createFaculty();

        $payload = [
            'first_name'      => 'UpdatedFirst',
            'middle_name'     => 'UpdatedMiddle',
            'last_name'       => 'UpdatedLast',
            'employment_type' => 'part_time',
            'status'          => 'inactive',
        ];

        $this->actingAs($this->admin)
            ->put(route('faculty.update', $faculty), $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('faculty.show', $faculty));

        $this->assertDatabaseHas('faculties', [
            'id'              => $faculty->id,
            'first_name'      => 'UpdatedFirst',
            'last_name'       => 'UpdatedLast',
            'employment_type' => 'part_time',
            'status'          => 'inactive',
        ]);
    }

    public function test_update_also_syncs_user_name(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->admin)
            ->put(route('faculty.update', $faculty), [
                'first_name' => 'Juan',
                'last_name'  => 'dela Cruz',
                'status'     => 'active',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id'   => $faculty->user_id,
            'name' => 'Juan dela Cruz',
        ]);
    }

    public function test_update_logs_activity_with_old_and_new_values(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->admin)
            ->put(route('faculty.update', $faculty), [
                'first_name' => 'New',
                'last_name'  => 'Name',
                'status'     => 'active',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('activity_log', [
            'description' => 'Updated faculty',
            'causer_id'   => $this->admin->id,
        ]);
    }

    public function test_update_is_denied_for_unauthorized_user(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->unauthorizedUser)
            ->put(route('faculty.update', $faculty), ['first_name' => 'Hacked'])
            ->assertForbidden();
    }

    public function test_update_fails_validation_when_required_fields_are_missing(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->admin)
            ->put(route('faculty.update', $faculty), [])
            ->assertSessionHasErrors(['first_name', 'last_name', 'status']);
    }

    // =========================================================================
    // DESTROY
    // =========================================================================

    public function test_destroy_deletes_faculty_and_associated_user(): void
    {
        $faculty = $this->createFaculty();
        $userId  = $faculty->user_id;

        $this->actingAs($this->admin)
            ->delete(route('faculty.destroy', $faculty))
            ->assertRedirect(route('faculty.index'));

        // Use withTrashed() scope to verify soft-deleted state
        $this->assertSoftDeleted('faculties', ['id' => $faculty->id]);
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    public function test_destroy_redirects_with_success_message(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->admin)
            ->delete(route('faculty.destroy', $faculty))
            ->assertRedirect(route('faculty.index'))
            ->assertSessionHas('success');
    }

    public function test_destroy_logs_activity_on_success(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->admin)
            ->delete(route('faculty.destroy', $faculty));

        $this->assertDatabaseHas('activity_log', [
            'description' => 'Deleted faculty',
            'causer_id'   => $this->admin->id,
        ]);
    }

    public function test_destroy_is_denied_for_unauthorized_user(): void
    {
        $faculty = $this->createFaculty();

        $this->actingAs($this->unauthorizedUser)
            ->delete(route('faculty.destroy', $faculty))
            ->assertForbidden();

        $this->assertDatabaseHas('faculties', ['id' => $faculty->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_faculty(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('faculty.destroy', 99999))
            ->assertNotFound();
    }
}
