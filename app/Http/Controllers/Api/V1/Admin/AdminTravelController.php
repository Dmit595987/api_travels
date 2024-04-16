<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminTravelRequest;
use App\Http\Requests\Admin\AdminUpdateTravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;

class AdminTravelController extends Controller
{
    public function store(AdminTravelRequest $request)
    {
        $travel = Travel::create($request->validated());

        return new TravelResource($travel);
    }

    public function update(Travel $travel, AdminUpdateTravelRequest $request)
    {
        $travel->update($request->validated());

        return new TravelResource($travel);
    }
}
