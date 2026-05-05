<?php

namespace Tests\Feature\Curriculum;

use Tests\TestCase;
use App\Models\User;
use App\Models\Section;
use App\Models\GradeLevel;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectionControllerTest extends TestCase
{
    use RefreshDatabase;

    private const PERM_VIEW   = 'view sections';
    private const PERM_CREATE = 'create sections';
    private const PERM_EDIT   = 'edit sections';
    private const PERM_DELETE = 'delete sections';

    private const STATUS_ACTIVE   = 'active';
    private const STATUS_INACTIVE = 'inactive';

    protected function setUp(): void
    {
        parent::setUp();

        self::$levelCounter = 1;

        foreach ([self::PERM_VIEW, self::PERM_CREATE, self::PERM_EDIT, self::PERM_DELETE] as $perm) {
            Permission::findOrCreate($perm);
        }
    }

    private function userWith(string ...$permissions): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        return $user;
    }

    private function userWithout(): User
    {
        return User::factory()->create();
    }

    /**
     * Creates a GradeLevel directly.
     * Level auto-increments per test run so multiple calls don't collide.
     */
    private static int $levelCounter = 1;

    private function createGradeLevel(?string $name = null): GradeLevel
    {
        $level = self::$levelCounter++;

        return GradeLevel::create([
            'name'      => $name ?? "Grade {$level}",
            'level'     => $level,
            'is_active' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Creates a Section directly (model has no HasFactory).
     */
    private function createSection(?GradeLevel $gradeLevel = null): Section
    {
        $gradeLevel ??= $this->createGradeLevel();

        return Section::create([
            'name'           => 'Test Section',
            'grade_level_id' => $gradeLevel->id,
            'is_active'      => self::STATUS_ACTIVE,
        ]);
    }

    // =========================================================================
    // INDEX
    // =========================================================================

    public function test_index_returns_200_with_permission(): void
    {
        $this->actingAs($this->userWith(self::PERM_VIEW))
            ->get(route('sections.index'))
            ->assertOk();
    }

    public function test_index_renders_correct_view(): void
    {
        $this->actingAs($this->userWith(self::PERM_VIEW))
            ->get(route('sections.index'))
            ->assertViewIs('sections.index');
    }

    public function test_index_returns_403_without_permission(): void
    {
        $this->actingAs($this->userWithout())
            ->get(route('sections.index'))
            ->assertForbidden();
    }

    // =========================================================================
    // CREATE
    // =========================================================================

    public function test_create_returns_200_with_permission(): void
    {
        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->get(route('sections.create'))
            ->assertOk();
    }

    public function test_create_renders_correct_view(): void
    {
        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->get(route('sections.create'))
            ->assertViewIs('sections.create');
    }

    public function test_create_passes_grade_levels_to_view(): void
    {
        $this->createGradeLevel('Grade 1');
        $this->createGradeLevel('Grade 2');
        $this->createGradeLevel('Grade 3');

        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->get(route('sections.create'))
            ->assertViewHas('gradeLevels', fn($gradeLevels) => $gradeLevels->count() === 3);
    }

    public function test_create_returns_403_without_permission(): void
    {
        $this->actingAs($this->userWithout())
            ->get(route('sections.create'))
            ->assertForbidden();
    }

    // =========================================================================
    // STORE
    // =========================================================================

    public function test_store_creates_section_and_redirects(): void
    {
        $gradeLevel = $this->createGradeLevel();

        $payload = [
            'name'           => 'Section A',
            'grade_level_id' => $gradeLevel->id,
            'is_active'      => self::STATUS_ACTIVE,
        ];

        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->post(route('sections.store'), $payload)
            ->assertSessionHas('success', 'Section created successfully.');

        $section = Section::where('name', 'Section A')->firstOrFail();

        $this->assertDatabaseHas('sections', $payload);

        $this->actingAs($this->userWith(self::PERM_VIEW))
            ->get(route('sections.show', $section))
            ->assertOk();
    }

    public function test_store_creates_inactive_section(): void
    {
        $gradeLevel = $this->createGradeLevel();

        $payload = [
            'name'           => 'Inactive Section',
            'grade_level_id' => $gradeLevel->id,
            'is_active'      => self::STATUS_INACTIVE,
        ];

        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->post(route('sections.store'), $payload)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('sections', $payload);
    }

    public function test_store_fails_validation_when_name_is_missing(): void
    {
        $gradeLevel = $this->createGradeLevel();

        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->post(route('sections.store'), [
                'grade_level_id' => $gradeLevel->id,
                'is_active'      => self::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_store_fails_validation_when_grade_level_id_is_missing(): void
    {
        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->post(route('sections.store'), [
                'name'      => 'Section A',
                'is_active' => self::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('grade_level_id');
    }

    public function test_store_fails_validation_when_is_active_is_missing(): void
    {
        $gradeLevel = $this->createGradeLevel();

        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->post(route('sections.store'), [
                'name'           => 'Section A',
                'grade_level_id' => $gradeLevel->id,
            ])
            ->assertSessionHasErrors('is_active');
    }

    public function test_store_fails_validation_when_is_active_is_invalid(): void
    {
        $gradeLevel = $this->createGradeLevel();

        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->post(route('sections.store'), [
                'name'           => 'Section A',
                'grade_level_id' => $gradeLevel->id,
                'is_active'      => 'yes',
            ])
            ->assertSessionHasErrors('is_active');
    }

    public function test_store_fails_validation_when_grade_level_does_not_exist(): void
    {
        $this->actingAs($this->userWith(self::PERM_CREATE))
            ->post(route('sections.store'), [
                'name'           => 'Section A',
                'grade_level_id' => 9999,
                'is_active'      => self::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('grade_level_id');
    }

    public function test_store_returns_403_without_permission(): void
    {
        $gradeLevel = $this->createGradeLevel();

        $this->actingAs($this->userWithout())
            ->post(route('sections.store'), [
                'name'           => 'Section A',
                'grade_level_id' => $gradeLevel->id,
                'is_active'      => self::STATUS_ACTIVE,
            ])
            ->assertForbidden();
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    public function test_show_returns_200_with_permission(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWith(self::PERM_VIEW))
            ->get(route('sections.show', $section))
            ->assertOk();
    }

    public function test_show_renders_correct_view(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWith(self::PERM_VIEW))
            ->get(route('sections.show', $section))
            ->assertViewIs('sections.show');
    }

    public function test_show_passes_section_with_grade_level_loaded(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWith(self::PERM_VIEW))
            ->get(route('sections.show', $section))
            ->assertViewHas('section', fn($s) => $s->relationLoaded('gradeLevel'));
    }

    public function test_show_returns_404_for_nonexistent_section(): void
    {
        $this->actingAs($this->userWith(self::PERM_VIEW))
            ->get(route('sections.show', 9999))
            ->assertNotFound();
    }

    public function test_show_returns_403_without_permission(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWithout())
            ->get(route('sections.show', $section))
            ->assertForbidden();
    }

    // =========================================================================
    // EDIT
    // =========================================================================

    public function test_edit_returns_200_with_permission(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWith(self::PERM_EDIT))
            ->get(route('sections.edit', $section))
            ->assertOk();
    }

    public function test_edit_renders_correct_view(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWith(self::PERM_EDIT))
            ->get(route('sections.edit', $section))
            ->assertViewIs('sections.edit');
    }

    public function test_edit_passes_section_and_grade_levels_to_view(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWith(self::PERM_EDIT))
            ->get(route('sections.edit', $section))
            ->assertViewHas('section', fn($s) => $s->is($section))
            ->assertViewHas('gradeLevels', fn($gradeLevels) => $gradeLevels->isNotEmpty());
    }

    public function test_edit_returns_404_for_nonexistent_section(): void
    {
        $this->actingAs($this->userWith(self::PERM_EDIT))
            ->get(route('sections.edit', 9999))
            ->assertNotFound();
    }

    public function test_edit_returns_403_without_permission(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWithout())
            ->get(route('sections.edit', $section))
            ->assertForbidden();
    }

    // =========================================================================
    // UPDATE
    // =========================================================================

    public function test_update_saves_changes_and_redirects(): void
    {
        $gradeLevel = $this->createGradeLevel();
        $section    = $this->createSection($gradeLevel);

        $payload = [
            'name'           => 'Updated Section',
            'grade_level_id' => $gradeLevel->id,
            'is_active'      => self::STATUS_INACTIVE,
        ];

        $this->actingAs($this->userWith(self::PERM_EDIT))
            ->put(route('sections.update', $section), $payload)
            ->assertRedirect(route('sections.show', $section))
            ->assertSessionHas('success', 'Section updated successfully.');

        $this->assertDatabaseHas('sections', array_merge(['id' => $section->id], $payload));
    }

    public function test_update_fails_validation_when_name_is_missing(): void
    {
        $gradeLevel = $this->createGradeLevel();
        $section    = $this->createSection($gradeLevel);

        $this->actingAs($this->userWith(self::PERM_EDIT))
            ->put(route('sections.update', $section), [
                'grade_level_id' => $gradeLevel->id,
                'is_active'      => self::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_update_fails_validation_when_is_active_is_invalid(): void
    {
        $gradeLevel = $this->createGradeLevel();
        $section    = $this->createSection($gradeLevel);

        $this->actingAs($this->userWith(self::PERM_EDIT))
            ->put(route('sections.update', $section), [
                'name'           => 'Updated Section',
                'grade_level_id' => $gradeLevel->id,
                'is_active'      => 'yes',
            ])
            ->assertSessionHasErrors('is_active');
    }

    public function test_update_returns_404_for_nonexistent_section(): void
    {
        $gradeLevel = $this->createGradeLevel();

        $this->actingAs($this->userWith(self::PERM_EDIT))
            ->put(route('sections.update', 9999), [
                'name'           => 'Updated Section',
                'grade_level_id' => $gradeLevel->id,
                'is_active'      => self::STATUS_ACTIVE,
            ])
            ->assertNotFound();
    }

    public function test_update_returns_403_without_permission(): void
    {
        $gradeLevel = $this->createGradeLevel();
        $section    = $this->createSection($gradeLevel);

        $this->actingAs($this->userWithout())
            ->put(route('sections.update', $section), [
                'name'           => 'Updated Section',
                'grade_level_id' => $gradeLevel->id,
                'is_active'      => self::STATUS_ACTIVE,
            ])
            ->assertForbidden();
    }

    // =========================================================================
    // DESTROY
    // =========================================================================

    public function test_destroy_deletes_section_and_redirects(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWith(self::PERM_DELETE))
            ->delete(route('sections.destroy', $section))
            ->assertRedirect(route('sections.index'))
            ->assertSessionHas('success', "Section 'Test Section' deleted successfully.");

        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_section(): void
    {
        $this->actingAs($this->userWith(self::PERM_DELETE))
            ->delete(route('sections.destroy', 9999))
            ->assertNotFound();
    }

    public function test_destroy_returns_403_without_permission(): void
    {
        $section = $this->createSection();

        $this->actingAs($this->userWithout())
            ->delete(route('sections.destroy', $section))
            ->assertForbidden();
    }
}
