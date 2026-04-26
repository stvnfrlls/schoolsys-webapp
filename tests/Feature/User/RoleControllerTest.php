<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->adminUser->givePermissionTo([
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
        ]);

        $this->regularUser = User::factory()->create();
    }

    // -------------------------------------------------------------------------
    // INDEX — DataTable HTML page
    // -------------------------------------------------------------------------

    #[Test]
    public function authenticated_admin_can_access_roles_index_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('roles.index');
    }

    #[Test]
    public function unauthenticated_user_is_redirected_from_roles_index(): void
    {
        $response = $this->get(route('roles.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthorized_user_cannot_access_roles_index(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('roles.index'));

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // INDEX — Yajra DataTables AJAX
    // -------------------------------------------------------------------------

    #[Test]
    public function roles_index_returns_datatable_json_when_ajax_request(): void
    {
        // 'Admin' already exists from seeder — use a different name
        Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('roles.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);
    }

    #[Test]
    public function datatable_data_array_contains_expected_role_fields(): void
    {
        Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('roles.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertNotEmpty($data);

        $firstRow = $data[0];
        $this->assertArrayHasKey('id', $firstRow);
        $this->assertArrayHasKey('name', $firstRow);
        $this->assertArrayHasKey('guard_name', $firstRow);
    }

    #[Test]
    public function datatable_supports_server_side_search(): void
    {
        Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        Role::create(['name' => 'Viewer', 'guard_name' => 'web']);

        $columns = [
            ['data' => 'id',                'name' => 'id',                'searchable' => 'false', 'orderable' => 'true',  'search' => ['value' => '', 'regex' => 'false']],
            ['data' => 'name',              'name' => 'name',              'searchable' => 'true',  'orderable' => 'true',  'search' => ['value' => '', 'regex' => 'false']],
            ['data' => 'permissions_count', 'name' => 'permissions_count', 'searchable' => 'false', 'orderable' => 'true',  'search' => ['value' => '', 'regex' => 'false']],
            ['data' => 'users_count',       'name' => 'users_count',       'searchable' => 'false', 'orderable' => 'true',  'search' => ['value' => '', 'regex' => 'false']],
            ['data' => 'created_at',        'name' => 'created_at',        'searchable' => 'false', 'orderable' => 'true',  'search' => ['value' => '', 'regex' => 'false']],
            ['data' => 'action',            'name' => 'action',            'searchable' => 'false', 'orderable' => 'false', 'search' => ['value' => '', 'regex' => 'false']],
        ];

        $response = $this->actingAs($this->adminUser)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('roles.index') . '?' . http_build_query([
                'draw'    => 1,
                'start'   => 0,
                'length'  => 10,
                'search'  => ['value' => 'SuperAdmin', 'regex' => 'false'],
                'columns' => $columns,
            ]));

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('SuperAdmin', $data[0]['name']);
    }

    #[Test]
    public function datatable_supports_pagination(): void
    {
        foreach (range(1, 20) as $i) {
            Role::create(['name' => "Bulk Role {$i}", 'guard_name' => 'web']);
        }

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('roles.index') . '?' . http_build_query([
                'draw'   => 1,
                'start'  => 0,
                'length' => 5,
            ]), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(5, $data);
        $this->assertGreaterThanOrEqual(20, $response->json('recordsTotal'));
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_access_create_role_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('roles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('roles.create');
    }

    #[Test]
    public function create_page_contains_available_permissions(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('roles.create'));

        $response->assertStatus(200);
        $response->assertViewHas('permissions');
    }

    #[Test]
    public function unauthorized_user_cannot_access_create_role_page(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('roles.create'));

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // STORE
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_create_a_role(): void
    {
        $p1 = Permission::create(['name' => 'test perm 1', 'guard_name' => 'web']);
        $p2 = Permission::create(['name' => 'test perm 2', 'guard_name' => 'web']);

        $payload = [
            'name'        => 'New Role',
            'guard_name'  => 'web',
            'permissions' => [$p1->id, $p2->id],
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('roles.store'), $payload);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name'       => 'New Role',
            'guard_name' => 'web',
        ]);

        $role = Role::where('name', 'New Role')->first();
        $this->assertCount(2, $role->permissions);
    }

    #[Test]
    public function store_fails_when_name_is_missing(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('roles.store'), [
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function store_fails_when_role_name_already_exists(): void
    {
        Role::create(['name' => 'Duplicate Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('roles.store'), [
                'name'       => 'Duplicate Role',
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function store_fails_when_name_exceeds_max_length(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('roles.store'), [
                'name'       => str_repeat('a', 256),
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function unauthorized_user_cannot_store_a_role(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('roles.store'), [
                'name'       => 'Some Role',
                'guard_name' => 'web',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('roles', ['name' => 'Some Role']);
    }

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_view_a_role(): void
    {
        $role = Role::create(['name' => 'Viewable Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('roles.show', $role));

        $response->assertStatus(200);
        $response->assertViewIs('roles.show');
        $response->assertViewHas('role', $role);
    }

    #[Test]
    public function show_returns_404_for_non_existent_role(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('roles.show', 9999));

        $response->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_access_edit_role_page(): void
    {
        $role = Role::create(['name' => 'Editable Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('roles.edit', $role));

        $response->assertStatus(200);
        $response->assertViewIs('roles.edit');
        $response->assertViewHas('role', $role);
        $response->assertViewHas('permissions');
    }

    #[Test]
    public function unauthorized_user_cannot_access_edit_role_page(): void
    {
        $role = Role::create(['name' => 'Editable Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->regularUser)
            ->get(route('roles.edit', $role));

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_update_a_role(): void
    {
        $role = Role::create(['name' => 'Old Name', 'guard_name' => 'web']);
        $p1   = Permission::create(['name' => 'update perm 1', 'guard_name' => 'web']);
        $p2   = Permission::create(['name' => 'update perm 2', 'guard_name' => 'web']);
        $p3   = Permission::create(['name' => 'update perm 3', 'guard_name' => 'web']);

        $payload = [
            'name'        => 'Updated Name',
            'guard_name'  => 'web',
            'permissions' => [$p1->id, $p2->id, $p3->id],
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('roles.update', $role), $payload);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Updated Name']);

        $role->refresh();
        $this->assertCount(3, $role->permissions);
    }

    #[Test]
    public function update_fails_with_missing_name(): void
    {
        $role = Role::create(['name' => 'Some Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('roles.update', $role), [
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function update_fails_when_name_is_taken_by_another_role(): void
    {
        $roleA = Role::create(['name' => 'Role A', 'guard_name' => 'web']);
        $roleB = Role::create(['name' => 'Role B', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('roles.update', $roleB), [
                'name'       => 'Role A',
                'guard_name' => 'web',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function update_allows_keeping_same_name_for_same_role(): void
    {
        $role = Role::create(['name' => 'Same Name', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->put(route('roles.update', $role), [
                'name'       => 'Same Name',
                'guard_name' => 'web',
            ]);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function unauthorized_user_cannot_update_a_role(): void
    {
        $role = Role::create(['name' => 'Protected Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->regularUser)
            ->put(route('roles.update', $role), [
                'name'       => 'Hacked Name',
                'guard_name' => 'web',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Protected Role']);
    }

    // -------------------------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_delete_a_role(): void
    {
        $role = Role::create(['name' => 'Deletable Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('roles.destroy', $role));

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    #[Test]
    public function delete_returns_404_for_non_existent_role(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->delete(route('roles.destroy', 9999));

        $response->assertNotFound();
    }

    #[Test]
    public function unauthorized_user_cannot_delete_a_role(): void
    {
        $role = Role::create(['name' => 'Protected Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->regularUser)
            ->delete(route('roles.destroy', $role));

        $response->assertForbidden();
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    #[Test]
    public function cannot_delete_role_assigned_to_users(): void
    {
        $role = Role::create(['name' => 'Assigned Role', 'guard_name' => 'web']);
        $this->regularUser->assignRole($role);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('roles.destroy', $role));

        $this->assertTrue(
            $response->status() === 422 || $response->isRedirect()
        );

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    // -------------------------------------------------------------------------
    // DataTables — Action Buttons
    // -------------------------------------------------------------------------

    #[Test]
    public function datatable_rows_include_action_buttons_for_admin(): void
    {
        Role::create(['name' => 'Action Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('roles.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        $html = collect($response->json('data'))->pluck('action')->implode('');

        $this->assertStringContainsString('Edit', $html);
        $this->assertStringContainsString('Delete', $html);
    }

    #[Test]
    public function datatable_returns_correct_records_total_count(): void
    {
        foreach (range(1, 10) as $i) {
            Role::create(['name' => "Count Role {$i}", 'guard_name' => 'web']);
        }

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('roles.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        $this->assertGreaterThanOrEqual(10, $response->json('recordsTotal'));
    }
}
