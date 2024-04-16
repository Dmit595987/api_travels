<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminTourRequest;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use App\Models\Travel;

class AdminTourController extends Controller
{
    public function store(Travel $travel, AdminTourRequest $request): TourResource
    {
        $tour = $travel->tours()->create($request->validated());

        return new TourResource($tour);
    }

    public function update(Tour $tour, AdminTourRequest $request): TourResource
    {
        $tour->update($request->validated());

        return new TourResource($tour);
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        return response()->json([], 204);
    }
}
