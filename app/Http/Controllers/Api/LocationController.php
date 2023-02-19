<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LocationCreateRequest;
use App\Models\LocationPoint;
use Illuminate\Http\Request;

class LocationController extends BaseApiController
{
    public function createLocation(LocationCreateRequest $request)
    {
        if(is_null($request->latitude) || $request->latitude < -90 || $request->latitude > 90) {
            return $this->sendError('Incorrect latitude!');
        }

        if(is_null($request->longitude) || $request->longitude < -180 || $request->longitude > 180) {
            return $this->sendError('Incorrect longitude!');
        }

        $existLocationPoint = LocationPoint::where('latitude', $request->latitude)
            ->where('longitude', $request->longitude)->first();

        if($existLocationPoint) {
            return $this->sendError('This location point already exist!');
        }

        $locationPoint = LocationPoint::create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $response = [
            'id' => $locationPoint->id,
            'latitude' => $locationPoint->latitude,
            'longitude' => $locationPoint->longitude,
        ];

        return $this->sendResponse($response, 'Location point successfuly created!');
    }
}
