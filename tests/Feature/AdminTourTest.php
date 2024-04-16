<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_tour_without_token()
    {
        $travel = Travel::factory()->create();
        $response = $this->postJson('/api/v1/admin/travels/'.$travel->slug.'/tours');
        $response->assertStatus(401);
    }

    public function test_not_admin_user_want_add_tour(): void
    {
        $travel = Travel::factory()->create();
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->slug.'/tours');
        $response->assertStatus(403);
    }

    public function test_admin_add_ok_tour_with_token(): void
    {
        $travel = Travel::factory()->create();
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->slug.'/tours', [
            'name' => 'Name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->slug.'/tours', [
            'name' => 'Name OK',
            'starting_date' => now()->addDays(3),
            'ending_date' => now()->addDays(10),
            'price' => 99.99,
        ]);

        $response->assertStatus(201);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');
        $response->assertJsonFragment(['name' => 'Name OK']);

    }
}
