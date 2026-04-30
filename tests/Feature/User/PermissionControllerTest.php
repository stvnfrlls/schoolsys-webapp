<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Spatie caches roles/permissions — always clear before each test
        // to prevent stale state from bleeding between tests.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole($this->adminRole);
    }

    // -------------------------------------------------------------------------
    // Authentication guard
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_is_redirected_from_index(): void
    {
        $this->get(route('permissions.index'))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_index_renders_datatable_view(): void
    {
        // A standard (non-AJAX) request renders the wrapper Blade view.
        $this->actingAs($this->admin)
            ->get(route('permissions.index'))
            ->assertOk()
            ->assertViewIs('permissions.index');
    }

    public function test_index_returns_json_for_datatable_ajax_request(): void
    {
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'view reports']);

        // Yajra DataTables checks $request->ajax(), which requires the
        // X-Requested-With header — not just Accept: application/json.
        // getJson() alone is not enough; withHeaders() is needed.
        $this->actingAs($this->admin)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('permissions.index'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    // -------------------------------------------------------------------------
    // create
    // -------------------------------------------------------------------------

    public function test_create_renders_form_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('permissions.create'))
            ->assertOk()
            ->assertViewIs('permissions.create');
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_store_creates_permission_and_redirects_with_success(): void
    {
        $this->actingAs($this->admin)
            ->post(route('permissions.store'), ['name' => 'edit articles', 'guard_name' => 'web'])
            ->assertRedirect(route('permissions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', ['name' => 'edit articles']);
    }

    public function test_store_fails_validation_when_name_is_missing(): void
    {
        $this->actingAs($this->admin)
            ->post(route('permissions.store'), ['name' => ''])
            ->assertSessionHasErrors('name');

        // Nothing was persisted
        $this->assertDatabaseCount('permissions', 0);
    }

    public function test_store_fails_validation_when_name_already_exists(): void
    {
        Permission::create(['name' => 'edit articles']);

        $this->actingAs($this->admin)
            ->post(route('permissions.store'), ['name' => 'edit articles', 'guard_name' => 'web'])
            ->assertSessionHasErrors('name');

        // Still only one record in the table
        $this->assertDatabaseCount('permissions', 1);
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    public function test_show_renders_view_with_correct_permission(): void
    {
        $permission = Permission::create(['name' => 'view reports']);

        $this->actingAs($this->admin)
            ->get(route('permissions.show', $permission))
            ->assertOk()
            ->assertViewIs('permissions.show')
            ->assertViewHas('permission', $permission);
    }

    public function test_show_returns_404_for_nonexistent_permission(): void
    {
        $this->actingAs($this->admin)
            ->get(route('permissions.show', 999))
            ->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // edit
    // -------------------------------------------------------------------------

    public function test_edit_renders_form_with_correct_permission(): void
    {
        $permission = Permission::create(['name' => 'view reports']);

        $this->actingAs($this->admin)
            ->get(route('permissions.edit', $permission))
            ->assertOk()
            ->assertViewIs('permissions.edit')
            ->assertViewHas('permission', $permission);
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_modifies_permission_and_redirects_with_success(): void
    {
        $permission = Permission::create(['name' => 'old name']);

        $this->actingAs($this->admin)
            ->put(route('permissions.update', $permission), ['name' => 'new name', 'guard_name' => 'web'])
            ->assertRedirect(route('permissions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', ['name' => 'new name']);
        $this->assertDatabaseMissing('permissions', ['name' => 'old name']);
    }

    public function test_update_fails_validation_when_name_is_missing(): void
    {
        $permission = Permission::create(['name' => 'some permission']);

        $this->actingAs($this->admin)
            ->put(route('permissions.update', $permission), ['name' => '', 'guard_name' => 'web'])
            ->assertSessionHasErrors('name');

        // Original record must be untouched
        $this->assertDatabaseHas('permissions', ['name' => 'some permission']);
    }

    public function test_update_fails_validation_when_name_already_taken_by_another_permission(): void
    {
        $permissionA = Permission::create(['name' => 'permission a']);
        Permission::create(['name' => 'permission b']);

        $this->actingAs($this->admin)
            ->put(route('permissions.update', $permissionA), ['name' => 'permission b', 'guard_name' => 'web'])
            ->assertSessionHasErrors('name');
    }

    public function test_update_succeeds_when_name_is_unchanged(): void
    {
        // unique rule should ignore the record's own id (i.e. Rule::unique()->ignore())
        $permission = Permission::create(['name' => 'same name']);

        $this->actingAs($this->admin)
            ->put(route('permissions.update', $permission), ['name' => 'same name', 'guard_name' => 'web'])
            ->assertRedirect(route('permissions.index'))
            ->assertSessionHas('success');
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_destroy_deletes_permission_and_redirects_with_success(): void
    {
        $permission = Permission::create(['name' => 'delete me']);

        $this->actingAs($this->admin)
            ->delete(route('permissions.destroy', $permission))
            ->assertRedirect(route('permissions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('permissions', ['name' => 'delete me']);
    }

    public function test_destroy_prevents_deletion_of_permission_assigned_to_admin_role(): void
    {
        $permission = Permission::create(['name' => 'protected permission']);
        $this->adminRole->givePermissionTo($permission);

        $this->actingAs($this->admin)
            ->delete(route('permissions.destroy', $permission))
            ->assertRedirect(route('permissions.index'))
            ->assertSessionHas('error');

        // Permission must still exist
        $this->assertDatabaseHas('permissions', ['name' => 'protected permission']);
    }

    public function test_destroy_allows_deletion_of_permission_assigned_only_to_non_admin_roles(): void
    {
        $editorRole = Role::create(['name' => 'editor']);
        $permission = Permission::create(['name' => 'manage drafts']);
        $editorRole->givePermissionTo($permission);

        $this->actingAs($this->admin)
            ->delete(route('permissions.destroy', $permission))
            ->assertRedirect(route('permissions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('permissions', ['name' => 'manage drafts']);
    }

    public function test_destroy_returns_404_for_nonexistent_permission(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('permissions.destroy', 999))
            ->assertNotFound();
    }
}
