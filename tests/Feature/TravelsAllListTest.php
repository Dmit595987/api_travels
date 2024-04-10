<?php

namespace Tests\Feature;

use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelsAllListTest extends TestCase
{
    use RefreshDatabase;
    public function test_travels_list_answer_is_ok(): void
    {
        Travel::factory(16)->create(['is_public' => true]);

        $response = $this->get('/api/v1/travels');
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_travels_list_only_public(): void
    {
        Travel::factory(1)->create(['is_public' => true]);
        Travel::factory(1)->create(['is_public' => false]);

        $response = $this->get('/api/v1/travels');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}
