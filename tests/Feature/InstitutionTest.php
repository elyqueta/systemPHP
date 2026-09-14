<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InstitutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_institution(): void
    {
        Role::firstOrCreate(['name' => 'super_admin']);
        $admin = User::factory()->create()->assignRole('super_admin');

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/institutions', [
            'name' => 'Kwanza Tech Lda',
            'tax_id' => '5417896321',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('institutions', ['tax_id' => '5417896321']);
    }

    public function test_user_without_permission_cannot_create_institution(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/institutions', [
            'name' => 'Tentativa Não Autorizada',
        ]);

        $response->assertForbidden();
    }
}
