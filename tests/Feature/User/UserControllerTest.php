<?php

namespace Tests\Feature\User;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * UserControllerTest
 *
 * Covers: index, create, store, show, edit, update, destroy
 * Auth:   Laravel Breeze (session-based)
 * Extras: Spatie roles, SoftDeletes, Yajra DataTable (AJAX)
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $faculty;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
        ]);

        $this->seed(RolePermissionSeeder::class);

        $this->admin   = User::factory()->create(['status' => 'active']);
        $this->faculty = User::factory()->create(['status' => 'active']);
        $this->student = User::factory()->create(['status' => 'active']);

        $this->admin->assignRole('Admin');
        $this->faculty->assignRole('Faculty');
        $this->student->assignRole('Student');
    }

    // =========================================================================
    // AUTHENTICATION — unauthenticated users must be redirected to login
    // =========================================================================

    #[Test]
    public function unauthenticated_user_is_redirected_from_index(): void
    {
        $this->get(route('users.index'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_is_redirected_from_create(): void
    {
        $this->get(route('users.create'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_cannot_store_a_user(): void
    {
        $this->post(route('users.store'), [])
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_is_redirected_from_show(): void
    {
        $user = User::factory()->create();

        $this->get(route('users.show', $user))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_is_redirected_from_edit(): void
    {
        $user = User::factory()->create();

        $this->get(route('users.edit', $user))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_cannot_update_a_user(): void
    {
        $user = User::factory()->create();

        $this->put(route('users.update', $user), [])
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_cannot_delete_a_user(): void
    {
        $user = User::factory()->create();

        $this->delete(route('users.destroy', $user))
            ->assertRedirect(route('login'));
    }

    // =========================================================================
    // INDEX
    // =========================================================================

    #[Test]
    public function authenticated_user_can_view_users_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertViewIs('users.index');
    }

    #[Test]
    public function index_returns_json_for_datatable_ajax_request(): void
    {
        // Yajra checks for X-Requested-With, not just Accept: application/json
        $this->actingAs($this->admin)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('users.index'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    // =========================================================================
    // CREATE
    // =========================================================================

    #[Test]
    public function create_returns_view_with_all_roles(): void
    {
        $roleCount = Role::count();

        $this->actingAs($this->admin)
            ->get(route('users.create'))
            ->assertOk()
            ->assertViewIs('users.create')
            ->assertViewHas('roles', function ($roles) use ($roleCount) {
                return $roles->count() === $roleCount;
            });
    }

    // =========================================================================
    // STORE — happy paths
    // =========================================================================

    #[Test]
    public function store_creates_user_and_assigns_role(): void
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'New Faculty Member',
                'email'                 => 'newfaculty@school.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'Faculty',
                'status'                => 'active',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name'   => 'New Faculty Member',
            'email'  => 'newfaculty@school.com',
            'status' => 'active',
        ]);

        $user = User::where('email', 'newfaculty@school.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Faculty'));
    }

    #[Test]
    public function store_hashes_the_password(): void
    {
        $plainPassword = 'Password123!';

        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'Hashed User',
                'email'                 => 'hashed@school.com',
                'password'              => $plainPassword,
                'password_confirmation' => $plainPassword,
                'role'                  => 'Student',
                'status'                => 'active',
            ]);

        $user = User::where('email', 'hashed@school.com')->first();

        $this->assertNotNull($user);
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }

    #[Test]
    public function store_redirects_to_show_on_success(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'Redirect Check',
                'email'                 => 'redirect@school.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'Student',
                'status'                => 'active',
            ]);

        $user = User::where('email', 'redirect@school.com')->firstOrFail();
        $response->assertRedirect(route('users.show', $user));
    }

    // =========================================================================
    // STORE — validation failures
    // =========================================================================

    #[Test]
    public function store_fails_when_name_is_missing(): void
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'email'                 => 'test@school.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'Student',
                'status'                => 'active',
            ])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function store_fails_with_malformed_email(): void
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'Test',
                'email'                 => 'not-an-email',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'Student',
                'status'                => 'active',
            ])
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function store_fails_when_email_already_exists(): void
    {
        User::factory()->create(['email' => 'taken@school.com']);

        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'Duplicate',
                'email'                 => 'taken@school.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'Student',
                'status'                => 'active',
            ])
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function store_allows_reuse_of_soft_deleted_user_email(): void
    {
        $deleted = User::factory()->create(['email' => 'reusable@school.com']);
        $deleted->delete();

        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'Reused Email',
                'email'                 => 'reusable@school.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'Student',
                'status'                => 'active',
            ])
            ->assertSessionHasNoErrors();
    }

    #[Test]
    public function store_fails_when_password_confirmation_does_not_match(): void
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'Test',
                'email'                 => 'mismatch@school.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'DifferentPass!',
                'role'                  => 'Student',
                'status'                => 'active',
            ])
            ->assertSessionHasErrors('password');
    }

    #[Test]
    public function store_fails_with_a_nonexistent_role(): void
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'Test',
                'email'                 => 'badrole@school.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'SuperAdmin',
                'status'                => 'active',
            ])
            ->assertSessionHasErrors('role');
    }

    #[Test]
    public function store_fails_with_an_invalid_status_value(): void
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'Test',
                'email'                 => 'badstatus@school.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'Student',
                'status'                => 'banned',
            ])
            ->assertSessionHasErrors('status');
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    #[Test]
    public function show_returns_view_with_correct_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('users.show', $user))
            ->assertOk()
            ->assertViewIs('users.show')
            ->assertViewHas('user', $user);
    }

    #[Test]
    public function show_returns_404_for_nonexistent_user(): void
    {
        $this->actingAs($this->admin)
            ->get(route('users.show', 99999))
            ->assertNotFound();
    }

    #[Test]
    public function show_returns_404_for_soft_deleted_user(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->actingAs($this->admin)
            ->get(route('users.show', $user->id))
            ->assertNotFound();
    }

    // =========================================================================
    // EDIT
    // =========================================================================

    #[Test]
    public function edit_returns_view_with_user_and_roles(): void
    {
        $user      = User::factory()->create();
        $roleCount = Role::count();

        $this->actingAs($this->admin)
            ->get(route('users.edit', $user))
            ->assertOk()
            ->assertViewIs('users.edit')
            ->assertViewHas('user', $user)
            ->assertViewHas('roles', function ($roles) use ($roleCount) {
                return $roles->count() === $roleCount;
            });
    }

    // =========================================================================
    // UPDATE — happy paths
    // =========================================================================

    #[Test]
    public function update_changes_name_status_and_syncs_role(): void
    {
        $user = User::factory()->create(['name' => 'Old Name', 'status' => 'active']);
        $user->assignRole('Student');

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'name'   => 'Updated Name',
                'role'   => 'Faculty',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('users.show', $user))
            ->assertSessionHas('success');

        $user->refresh();

        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('inactive', $user->status);
        $this->assertTrue($user->hasRole('Faculty'));
        $this->assertFalse($user->hasRole('Student')); // synced, not added
    }

    #[Test]
    public function update_syncs_role_replacing_all_previous_roles(): void
    {
        // User has two roles before update
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $user->assignRole('Faculty');

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'name'   => $user->name,
                'role'   => 'Student',
                'status' => 'active',
            ]);

        $user->refresh();

        $this->assertTrue($user->hasRole('Student'));
        $this->assertFalse($user->hasRole('Admin'));
        $this->assertFalse($user->hasRole('Faculty'));
    }

    #[Test]
    public function update_does_not_modify_email_or_password(): void
    {
        // UpdateUserRequest only validates name, role, status
        // Any extra fields like email/password must be silently ignored
        $originalEmail    = 'original@school.com';
        $originalPassword = 'OriginalPass123!';

        $user = User::factory()->create([
            'email'    => $originalEmail,
            'password' => Hash::make($originalPassword),
        ]);

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'name'     => 'Updated',
                'email'    => 'injected@attacker.com', // must be ignored
                'password' => 'HackedPass123!',        // must be ignored
                'role'     => 'Student',
                'status'   => 'active',
            ]);

        $user->refresh();

        $this->assertEquals($originalEmail, $user->email);
        $this->assertTrue(Hash::check($originalPassword, $user->password));
        $this->assertFalse(Hash::check('HackedPass123!', $user->password));
    }

    // =========================================================================
    // UPDATE — validation failures
    // =========================================================================

    #[Test]
    public function update_fails_when_name_is_missing(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'role'   => 'Student',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function update_fails_with_an_invalid_status(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'name'   => 'Test',
                'role'   => 'Student',
                'status' => 'suspended',
            ])
            ->assertSessionHasErrors('status');
    }

    #[Test]
    public function update_fails_with_a_nonexistent_role(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'name'   => 'Test',
                'role'   => 'GhostRole',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('role');
    }

    // =========================================================================
    // DESTROY
    // =========================================================================

    #[Test]
    public function destroy_soft_deletes_the_user_and_redirects(): void
    {
        $user = User::factory()->create(['name' => 'Deletable User']);

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    #[Test]
    public function destroy_returns_404_for_nonexistent_user(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('users.destroy', 99999))
            ->assertNotFound();
    }

    #[Test]
    public function destroy_returns_404_for_already_soft_deleted_user(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $user->id))
            ->assertNotFound();
    }

    #[Test]
    public function destroy_success_message_contains_the_deleted_users_name(): void
    {
        $user = User::factory()->create(['name' => 'Juan dela Cruz']);

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $user))
            ->assertSessionHas('success', "User 'Juan dela Cruz' deleted successfully.");
    }
}
