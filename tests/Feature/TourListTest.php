<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourListTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_tours_list_by_travel_slug_returns_correct_tours(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }

    public function test_tours_list_returns_pagination(): void
    {

        $travel = Travel::factory()->create();
        Tour::factory(16)->create(['travel_id' => $travel->id]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_tour_price_correct(): void
    {
        $travel = Travel::factory()->create();
        Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => '123.45',
        ]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => '123.45']);
    }
}
