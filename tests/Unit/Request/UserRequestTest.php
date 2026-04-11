<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * UserRequestTest
 *
 * Unit-level validation tests for StoreUserRequest and UpdateUserRequest.
 * These test the rules() output directly, independent of HTTP routing.
 */
class UserRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function validateStore(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, (new StoreUserRequest())->rules());
    }

    private function validateUpdate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, (new UpdateUserRequest())->rules());
    }

    private function validStorePayload(array $overrides = []): array
    {
        return array_merge([
            'name'                  => 'Valid User',
            'email'                 => 'valid@school.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role'                  => 'Admin',
            'status'                => 'active',
        ], $overrides);
    }

    private function validUpdatePayload(array $overrides = []): array
    {
        return array_merge([
            'name'   => 'Updated Name',
            'role'   => 'Faculty',
            'status' => 'inactive',
        ], $overrides);
    }

    // =========================================================================
    // StoreUserRequest
    // =========================================================================

    #[Test]
    public function store_request_passes_with_all_valid_data(): void
    {
        $validator = $this->validateStore($this->validStorePayload());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function store_request_authorize_returns_true(): void
    {
        $request = new StoreUserRequest();

        $this->assertTrue($request->authorize());
    }

    // --- name ---

    #[Test]
    public function store_request_requires_name(): void
    {
        $validator = $this->validateStore(
            $this->validStorePayload(['name' => ''])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    #[Test]
    public function store_request_rejects_name_exceeding_255_characters(): void
    {
        $validator = $this->validateStore(
            $this->validStorePayload(['name' => str_repeat('a', 256)])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    // --- email ---

    #[Test]
    public function store_request_requires_email(): void
    {
        $validator = $this->validateStore(
            $this->validStorePayload(['email' => ''])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function store_request_rejects_malformed_email(): void
    {
        $validator = $this->validateStore(
            $this->validStorePayload(['email' => 'not-an-email'])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function store_request_rejects_duplicate_email_of_active_user(): void
    {
        User::factory()->create(['email' => 'taken@school.com']);

        $validator = $this->validateStore(
            $this->validStorePayload(['email' => 'taken@school.com'])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function store_request_allows_email_of_soft_deleted_user(): void
    {
        $deleted = User::factory()->create(['email' => 'deleted@school.com']);
        $deleted->delete();

        $validator = $this->validateStore(
            $this->validStorePayload(['email' => 'deleted@school.com'])
        );

        $this->assertFalse($validator->fails(), 'Soft-deleted email should be reusable.');
    }

    // --- password ---

    #[Test]
    public function store_request_requires_password(): void
    {
        $validator = $this->validateStore(
            $this->validStorePayload([
                'password'              => '',
                'password_confirmation' => '',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function store_request_rejects_password_shorter_than_8_characters(): void
    {
        $validator = $this->validateStore(
            $this->validStorePayload([
                'password'              => 'short',
                'password_confirmation' => 'short',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function store_request_rejects_mismatched_password_confirmation(): void
    {
        $validator = $this->validateStore(
            $this->validStorePayload([
                'password'              => 'Password123!',
                'password_confirmation' => 'Different123!',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    // --- role ---

    #[Test]
    public function store_request_accepts_valid_role_names(): void
    {
        foreach (['Admin', 'Faculty', 'Student'] as $role) {
            $validator = $this->validateStore(
                $this->validStorePayload([
                    'email' => uniqid('r_') . '@school.com',
                    'role'  => $role,
                ])
            );

            $this->assertFalse(
                $validator->fails(),
                "Role '$role' should be valid but failed: " . json_encode($validator->errors()->toArray())
            );
        }
    }

    #[Test]
    public function store_request_rejects_nonexistent_role(): void
    {
        $validator = $this->validateStore(
            $this->validStorePayload(['role' => 'SuperAdmin'])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('role', $validator->errors()->toArray());
    }

    // --- status ---

    #[Test]
    public function store_request_accepts_active_and_inactive_status(): void
    {
        foreach (['active', 'inactive'] as $status) {
            $validator = $this->validateStore(
                $this->validStorePayload([
                    'email'  => uniqid('s_') . '@school.com',
                    'status' => $status,
                ])
            );

            $this->assertFalse(
                $validator->fails(),
                "Status '$status' should be valid but failed."
            );
        }
    }

    #[Test]
    public function store_request_rejects_invalid_status_values(): void
    {
        foreach (['banned', 'pending', 'suspended', ''] as $badStatus) {
            $validator = $this->validateStore(
                $this->validStorePayload(['status' => $badStatus])
            );

            $this->assertTrue(
                $validator->fails(),
                "Status '$badStatus' should be invalid but passed."
            );
            $this->assertArrayHasKey('status', $validator->errors()->toArray());
        }
    }

    // =========================================================================
    // UpdateUserRequest
    // =========================================================================

    #[Test]
    public function update_request_passes_with_all_valid_data(): void
    {
        $validator = $this->validateUpdate($this->validUpdatePayload());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function update_request_authorize_returns_true(): void
    {
        $request = new UpdateUserRequest();

        $this->assertTrue($request->authorize());
    }

    #[Test]
    public function update_request_does_not_require_email(): void
    {
        // UpdateUserRequest intentionally has no email rule
        $rules = (new UpdateUserRequest())->rules();

        $this->assertArrayNotHasKey('email', $rules);
    }

    #[Test]
    public function update_request_does_not_require_password(): void
    {
        // UpdateUserRequest intentionally has no password rule
        $rules = (new UpdateUserRequest())->rules();

        $this->assertArrayNotHasKey('password', $rules);
    }

    // --- name ---

    #[Test]
    public function update_request_requires_name(): void
    {
        $validator = $this->validateUpdate(
            $this->validUpdatePayload(['name' => ''])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    #[Test]
    public function update_request_rejects_name_exceeding_255_characters(): void
    {
        $validator = $this->validateUpdate(
            $this->validUpdatePayload(['name' => str_repeat('x', 256)])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    // --- role ---

    #[Test]
    public function update_request_accepts_valid_role_names(): void
    {
        foreach (['Admin', 'Faculty', 'Student'] as $role) {
            $validator = $this->validateUpdate(
                $this->validUpdatePayload(['role' => $role])
            );

            $this->assertFalse(
                $validator->fails(),
                "Role '$role' should be valid on update."
            );
        }
    }

    #[Test]
    public function update_request_rejects_nonexistent_role(): void
    {
        $validator = $this->validateUpdate(
            $this->validUpdatePayload(['role' => 'GhostRole'])
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('role', $validator->errors()->toArray());
    }

    // --- status ---

    #[Test]
    public function update_request_accepts_active_and_inactive_status(): void
    {
        foreach (['active', 'inactive'] as $status) {
            $validator = $this->validateUpdate(
                $this->validUpdatePayload(['status' => $status])
            );

            $this->assertFalse(
                $validator->fails(),
                "Status '$status' should be valid on update."
            );
        }
    }

    #[Test]
    public function update_request_rejects_invalid_status_values(): void
    {
        foreach (['banned', 'pending', 'suspended', ''] as $badStatus) {
            $validator = $this->validateUpdate(
                $this->validUpdatePayload(['status' => $badStatus])
            );

            $this->assertTrue(
                $validator->fails(),
                "Status '$badStatus' should be invalid on update but passed."
            );
        }
    }
}
