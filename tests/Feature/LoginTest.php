<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token(): void
    {

        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);

    }

    public function test_login_return_abort_if_user_incorrect(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'user_incorrect@mail.com',
            'password' => 'dgdfhdghfnhfnhn11DD',
        ]);

        $response->assertStatus(422);
    }
}
