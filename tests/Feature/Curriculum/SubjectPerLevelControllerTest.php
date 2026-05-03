<?php

namespace Tests\Feature\Curriculum;

use Tests\TestCase;
use App\Models\User;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\SubjectPerLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

class SubjectPerLevelControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (
            [
                'view subject per level',
                'create subject per level',
                'edit subject per level',
                'delete subject per level',
            ] as $permission
        ) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->user = User::factory()->create();
    }

    private function givePermission(string ...$permissions): void
    {
        $this->user->givePermissionTo($permissions);
    }

    // -------------------------------------------------------------------------
    // Helpers — no HasFactory on Subject / SubjectPerLevel
    // -------------------------------------------------------------------------

    private function makeGradeLevel(array $attrs = []): GradeLevel
    {
        return GradeLevel::factory()->create($attrs);
    }

    private function makeSubject(array $attrs = []): Subject
    {
        return Subject::create(array_merge([
            'name'        => fake()->unique()->words(3, true),
            'code'        => strtoupper(fake()->unique()->lexify('???###')),
            'description' => fake()->sentence(),
            'is_active'    => true,
        ], $attrs));
    }

    private function makeSubjectPerLevel(array $attrs = []): SubjectPerLevel
    {
        return SubjectPerLevel::create(array_merge([
            'gradelevel_id'  => $this->makeGradeLevel()->id,
            'subject_id'     => $this->makeSubject()->id,
            'hours_per_week' => 3,
        ], $attrs));
    }

    private function validPayload(?GradeLevel $gradeLevel = null, ?Subject $subject = null): array
    {
        return [
            'gradelevel_id'  => ($gradeLevel ?? $this->makeGradeLevel())->id,
            'subject_id'     => ($subject    ?? $this->makeSubject())->id,
            'hours_per_week' => 4,
            'is_active'      => 'active',
        ];
    }

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_index_redirects_guests_to_login(): void
    {
        $this->get(route('subjectperlevel.index'))
            ->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $this->actingAs($this->user)
            ->get(route('subjectperlevel.index'))
            ->assertForbidden();
    }

    public function test_index_renders_view_for_authorized_user(): void
    {
        $this->givePermission('view subject per level');

        $this->actingAs($this->user)
            ->get(route('subjectperlevel.index'))
            ->assertOk()
            ->assertViewIs('subjectperlevel.index');
    }

    // -------------------------------------------------------------------------
    // create
    // -------------------------------------------------------------------------

    public function test_create_redirects_guests_to_login(): void
    {
        $this->get(route('subjectperlevel.create'))
            ->assertRedirect(route('login'));
    }

    public function test_create_is_forbidden_without_permission(): void
    {
        $this->actingAs($this->user)
            ->get(route('subjectperlevel.create'))
            ->assertForbidden();
    }

    public function test_create_renders_view_with_grade_levels_and_subjects(): void
    {
        $this->givePermission('create subject per level');

        $gradeLevel = $this->makeGradeLevel();
        $subject    = $this->makeSubject();

        $this->actingAs($this->user)
            ->get(route('subjectperlevel.create'))
            ->assertOk()
            ->assertViewIs('subjectperlevel.create')
            ->assertViewHas('gradeLevels', fn($levels)   => $levels->contains($gradeLevel))
            ->assertViewHas('subjects',    fn($subjects) => $subjects->contains($subject));
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_store_redirects_guests_to_login(): void
    {
        $this->post(route('subjectperlevel.store'), [])
            ->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $this->actingAs($this->user)
            ->post(route('subjectperlevel.store'), $this->validPayload())
            ->assertForbidden();
    }

    public function test_store_fails_validation_with_missing_fields(): void
    {
        $this->givePermission('create subject per level');

        $this->actingAs($this->user)
            ->post(route('subjectperlevel.store'), [])
            ->assertSessionHasErrors(['gradelevel_id', 'subject_id', 'is_active']);
    }

    public function test_store_creates_record_and_redirects(): void
    {
        $this->givePermission('create subject per level');

        $gradeLevel = $this->makeGradeLevel();
        $subject    = $this->makeSubject();
        $payload    = $this->validPayload($gradeLevel, $subject);

        $this->actingAs($this->user)
            ->post(route('subjectperlevel.store'), $payload)
            ->assertRedirect(route('subjectperlevel.index'))
            ->assertSessionHas('success', 'Subject assignment created successfully.');

        $this->assertDatabaseHas('subject_per_levels', [
            'gradelevel_id'  => $gradeLevel->id,
            'subject_id'     => $subject->id,
            'hours_per_week' => $payload['hours_per_week'],
        ]);
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    public function test_show_redirects_guests_to_login(): void
    {
        $record = $this->makeSubjectPerLevel();

        $this->get(route('subjectperlevel.show', $record))
            ->assertRedirect(route('login'));
    }

    public function test_show_is_forbidden_without_permission(): void
    {
        $record = $this->makeSubjectPerLevel();

        $this->actingAs($this->user)
            ->get(route('subjectperlevel.show', $record))
            ->assertForbidden();
    }

    public function test_show_renders_view_with_record(): void
    {
        $this->givePermission('view subject per level');

        $record = $this->makeSubjectPerLevel();

        $this->actingAs($this->user)
            ->get(route('subjectperlevel.show', $record))
            ->assertOk()
            ->assertViewIs('subjectperlevel.show')
            ->assertViewHas('subjectperlevel', $record);
    }

    // -------------------------------------------------------------------------
    // edit
    // -------------------------------------------------------------------------

    public function test_edit_redirects_guests_to_login(): void
    {
        $record = $this->makeSubjectPerLevel();

        $this->get(route('subjectperlevel.edit', $record))
            ->assertRedirect(route('login'));
    }

    public function test_edit_is_forbidden_without_permission(): void
    {
        $record = $this->makeSubjectPerLevel();

        $this->actingAs($this->user)
            ->get(route('subjectperlevel.edit', $record))
            ->assertForbidden();
    }

    public function test_edit_renders_view_with_record_and_options(): void
    {
        $this->givePermission('edit subject per level');

        $gradeLevel = $this->makeGradeLevel();
        $subject    = $this->makeSubject();
        $record     = $this->makeSubjectPerLevel();

        $this->actingAs($this->user)
            ->get(route('subjectperlevel.edit', $record))
            ->assertOk()
            ->assertViewIs('subjectperlevel.edit')
            ->assertViewHas('subjectperlevel', $record)
            ->assertViewHas('gradeLevels', fn($levels)   => $levels->contains($gradeLevel))
            ->assertViewHas('subjects',    fn($subjects) => $subjects->contains($subject));
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_redirects_guests_to_login(): void
    {
        $record = $this->makeSubjectPerLevel();

        $this->put(route('subjectperlevel.update', $record), [])
            ->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $record = $this->makeSubjectPerLevel();

        $this->actingAs($this->user)
            ->put(route('subjectperlevel.update', $record), $this->validPayload())
            ->assertForbidden();
    }

    public function test_update_fails_validation_with_missing_fields(): void
    {
        $this->givePermission('edit subject per level');

        $record = $this->makeSubjectPerLevel();

        $this->actingAs($this->user)
            ->put(route('subjectperlevel.update', $record), [])
            ->assertSessionHasErrors(['gradelevel_id', 'subject_id', 'is_active']);
    }

    public function test_update_persists_changes_and_redirects(): void
    {
        $this->givePermission('edit subject per level');

        $record     = $this->makeSubjectPerLevel();
        $newGrade   = $this->makeGradeLevel();
        $newSubject = $this->makeSubject();
        $payload    = $this->validPayload($newGrade, $newSubject);

        $this->actingAs($this->user)
            ->put(route('subjectperlevel.update', $record), $payload)
            ->assertRedirect(route('subjectperlevel.show', $record))
            ->assertSessionHas('success', 'Subject assignment updated successfully.');

        $this->assertDatabaseHas('subject_per_levels', [
            'id'             => $record->id,
            'gradelevel_id'  => $newGrade->id,
            'subject_id'     => $newSubject->id,
            'hours_per_week' => $payload['hours_per_week'],
        ]);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_destroy_redirects_guests_to_login(): void
    {
        $record = $this->makeSubjectPerLevel();

        $this->delete(route('subjectperlevel.destroy', $record))
            ->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $record = $this->makeSubjectPerLevel();

        $this->actingAs($this->user)
            ->delete(route('subjectperlevel.destroy', $record))
            ->assertForbidden();
    }

    public function test_destroy_deletes_record_and_redirects(): void
    {
        $this->givePermission('delete subject per level');

        $record = $this->makeSubjectPerLevel();

        $this->actingAs($this->user)
            ->delete(route('subjectperlevel.destroy', $record))
            ->assertRedirect(route('subjectperlevel.index'))
            ->assertSessionHas('success', 'Subject Assignment deleted successfully.');

        $this->assertDatabaseMissing('subject_per_levels', ['id' => $record->id]);
    }
}
