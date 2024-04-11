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

    public function test_tours_list_sorts_by_starting_date_correctly():void
    {
        $travel = Travel::factory()->create();
        $firstTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(1),
            'ending_date' => now()->addDays(3),
        ]);

        $lastTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(5),
            'ending_date' => now()->addDays(10),
        ]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?' . 'dateFrom=' . now()->addDays(1));
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $firstTour->id);
        $response->assertJsonPath('data.1.id', $lastTour->id);
    }

    public function test_tours_list_sorts_by_price_correctly():void
    {
        $travel = Travel::factory()->create();
        $tour1 = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 500,
        ]);

        $tour2 = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 300,
        ]);

        $tour3 = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
        ]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?' . 'sortBy=price&' . 'sortOrder=desc');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tour1->id);
        $response->assertJsonPath('data.1.id', $tour2->id);
        $response->assertJsonPath('data.2.id', $tour3->id);

    }

    public function test_tours_list_filters_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();

        $laterTour = Tour::factory()
            ->create([
                'travel_id' => $travel->id,
                'starting_date' => now()->addDays(2),
                'ending_date' => now()->addDays(3),
            ]);

        $earlierTour = Tour::factory()
            ->create([
                'travel_id' => $travel->id,
                'starting_date' => now(),
                'ending_date' => now()->addDays(1),
            ]);

        $endPoint = '/api/v1/travels/' . $travel->slug . '/tours';

        $response = $this->get($endPoint . '?dateFrom='.now()->addDay());
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing([ 'id' => $earlierTour->id] );
        $response->assertJsonFragment([ 'id' => $laterTour->id] );

        $response = $this->get($endPoint . '?dateFrom='.now()->addDays(5));
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endPoint . '?dateTo='.now()->addDays(5));
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment([ 'id' => $laterTour->id] );
        $response->assertJsonFragment([ 'id' => $earlierTour->id] );

        $response = $this->get($endPoint . '?dateTo='.now()->addDay());
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing([ 'id' => $laterTour->id] );
        $response->assertJsonFragment([ 'id' => $earlierTour->id] );

        $response = $this->get($endPoint . '?dateTo='.now()->subDay());
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endPoint . '?dateFrom='.now()->addDay(). '&dateTo=' . now()->addDays(5));
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing([ 'id' => $earlierTour->id] );
        $response->assertJsonFragment([ 'id' => $laterTour->id] );

    }

    public function test_tours_list_returns_validation_correctly(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->getJson('/api/v1/travels/' . $travel->slug . '/tours?dateFrom=abcde');
        $response->assertStatus(422);
        $response = $this->getJson('/api/v1/travels/' . $travel->slug . '/tours?priceFrom=abcde');
        $response->assertStatus(422);
    }
}
