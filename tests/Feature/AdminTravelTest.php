<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_travel_without_token(): void
    {
        $response = $this->postJson('/api/v1/admin/travels');
        $response->assertStatus(401);
    }

    public function test_not_admin_user_want_add_travel(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');
        $response->assertStatus(403);
    }

    public function test_admin_add_ok_travel_with_token(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Name OK',
            'is_public' => 1,
            'description' => 'description test',
            'number_of_days' => 3,
        ]);

        $response->assertStatus(201);

        $response = $this->get('/api/v1/travels');
        $response->assertJsonFragment(['name' => 'Name OK']);

    }

    public function test_ok_update_travel_with_user_editor()
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id, [
            'name' => 'Name new',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id, [
            'name' => 'Name new',
            'description' => 'description new',
            'number_of_days' => 23,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Name new']);
    }
}
