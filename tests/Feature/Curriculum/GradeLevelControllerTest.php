<?php

namespace Tests\Feature\Curriculum;

use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GradeLevelControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Adjust this if your app uses roles/permissions (e.g. Spatie).
        // Replace with the appropriate user factory state if needed.
        $this->user = User::factory()->create();

        $permissions = [
            'view grade levels',
            'create grade levels',
            'edit grade levels',
            'delete grade levels',
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
            ->get(route('gradelevels.index'))
            ->assertOk();
    }

    public function test_unauthenticated_user_cannot_access_index(): void
    {
        $this->get(route('gradelevels.index'))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function test_create_page_renders_successfully(): void
    {
        $this->actingAs($this->user)
            ->get(route('gradelevels.create'))
            ->assertOk()
            ->assertViewIs('gradelevels.create');
    }

    // -------------------------------------------------------------------------
    // STORE
    // -------------------------------------------------------------------------

    public function test_store_creates_grade_level_and_redirects(): void
    {
        $payload = [
            'name'      => 'Grade 1',
            'level'     => 1,
            'is_active' => 'active',
        ];

        $this->actingAs($this->user)
            ->post(route('gradelevels.store'), $payload)
            ->assertRedirect(route('gradelevels.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('grade_levels', [
            'name'      => 'Grade 1',
            'level'     => 1,
            'is_active' => 'active',
        ]);
    }

    public function test_store_fails_when_name_is_missing(): void
    {
        $this->actingAs($this->user)
            ->post(route('gradelevels.store'), [
                'level'     => 1,
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_store_fails_when_level_is_not_an_integer(): void
    {
        $this->actingAs($this->user)
            ->post(route('gradelevels.store'), [
                'name'      => 'Grade 1',
                'level'     => 'abc',
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('level');
    }

    public function test_store_fails_when_level_exceeds_maximum(): void
    {
        $this->actingAs($this->user)
            ->post(route('gradelevels.store'), [
                'name'      => 'Grade 1',
                'level'     => 3,
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('level');
    }

    public function test_store_fails_when_is_active_is_invalid(): void
    {
        $this->actingAs($this->user)
            ->post(route('gradelevels.store'), [
                'name'      => 'Grade 1',
                'level'     => 1,
                'is_active' => 'pending',
            ])
            ->assertSessionHasErrors('is_active');
    }

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------

    public function test_show_renders_successfully(): void
    {
        $gradeLevel = GradeLevel::factory()->create();

        $this->actingAs($this->user)
            ->get(route('gradelevels.show', $gradeLevel))
            ->assertOk()
            ->assertViewIs('gradelevels.show')
            ->assertViewHas('gradeLevel', $gradeLevel);
    }

    public function test_show_returns_404_for_nonexistent_grade_level(): void
    {
        $this->actingAs($this->user)
            ->get(route('gradelevels.show', 999))
            ->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------

    public function test_edit_page_renders_successfully(): void
    {
        $gradeLevel = GradeLevel::factory()->create();

        $this->actingAs($this->user)
            ->get(route('gradelevels.edit', $gradeLevel))
            ->assertOk()
            ->assertViewIs('gradelevels.edit')
            ->assertViewHas('gradeLevel', $gradeLevel);
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    public function test_update_modifies_grade_level_and_redirects(): void
    {
        $gradeLevel = GradeLevel::factory()->create([
            'name'      => 'Grade 1',
            'level'     => 1,
            'is_active' => 'active',
        ]);

        $this->actingAs($this->user)
            ->put(route('gradelevels.update', $gradeLevel), [
                'name'      => 'Grade 2',
                'level'     => 2,
                'is_active' => 'inactive',
            ])
            ->assertRedirect(route('gradelevels.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('grade_levels', [
            'id'        => $gradeLevel->id,
            'name'      => 'Grade 2',
            'level'     => 2,
            'is_active' => 'inactive',
        ]);
    }

    public function test_update_fails_when_name_is_missing(): void
    {
        $gradeLevel = GradeLevel::factory()->create();

        $this->actingAs($this->user)
            ->put(route('gradelevels.update', $gradeLevel), [
                'level'     => 1,
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_update_fails_when_level_is_not_an_integer(): void
    {
        $gradeLevel = GradeLevel::factory()->create();

        $this->actingAs($this->user)
            ->put(route('gradelevels.update', $gradeLevel), [
                'name'      => 'Grade 1',
                'level'     => 'abc',
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('level');
    }

    public function test_update_fails_when_level_exceeds_maximum(): void
    {
        $gradeLevel = GradeLevel::factory()->create();

        $this->actingAs($this->user)
            ->put(route('gradelevels.update', $gradeLevel), [
                'name'      => 'Grade 1',
                'level'     => 3,
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('level');
    }

    public function test_update_fails_when_is_active_is_invalid(): void
    {
        $gradeLevel = GradeLevel::factory()->create();

        $this->actingAs($this->user)
            ->put(route('gradelevels.update', $gradeLevel), [
                'name'      => 'Grade 1',
                'level'     => 1,
                'is_active' => 'pending',
            ])
            ->assertSessionHasErrors('is_active');
    }

    // -------------------------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------------------------

    public function test_destroy_deletes_grade_level_and_redirects(): void
    {
        $gradeLevel = GradeLevel::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('gradelevels.destroy', $gradeLevel))
            ->assertRedirect(route('gradelevels.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('grade_levels', ['id' => $gradeLevel->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_grade_level(): void
    {
        $this->actingAs($this->user)
            ->delete(route('gradelevels.destroy', 999))
            ->assertNotFound();
    }
}
