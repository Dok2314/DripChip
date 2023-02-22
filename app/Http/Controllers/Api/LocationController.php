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
            return $this->sendError('This location point already exist!',[],409);
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

    public function getInfo($locationId)
    {
        if(is_null($locationId) || $locationId <= 0) {
            return $this->sendError('Incorrect locationId!');
        }

        $locationPoint = LocationPoint::find($locationId);

        if($locationPoint) {
            $response = [
                'id' => $locationPoint->id,
                'latitude' => $locationPoint->latitude,
                'longitude' => $locationPoint->longitude,
            ];

            return $this->sendResponse($response, 'Location Point successfully received!');
        }

        return $this->sendError('Location Point with locationId = ' . $locationId . ' not found!');
    }

    public function updateLocation($locationId, Request $request)
    {
        if(is_null($locationId) || $locationId <= 0) {
            return $this->sendError('Incorrect pointId!');
        }

        if(is_null($request->latitude) || $request->latitude < -90 || $request->latitude > 90) {
            return $this->sendError('Incorrect latitude!');
        }

        if(is_null($request->longitude) || $request->longitude < -180 || $request->longitude > 180) {
            return $this->sendError('Incorrect longitude!');
        }

        $locationPoint = LocationPoint::find($locationId);

        $existLocationPoint = LocationPoint::where('latitude', $request->latitude)
            ->where('longitude', $request->longitude)->first();

        if(isset($existLocationPoint) && $existLocationPoint->id != $locationId) {
            return $this->sendError(
                sprintf(
                'Location Point with latitude = %s and longitude = %s  already exists!',
                    $request->latitude, $request->longitude)
            );
        }

        if($locationPoint) {
            $locationPoint->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            $response = [
                'id' => $locationPoint->id,
                'latitude' => $locationPoint->latitude,
                'longitude' => $locationPoint->longitude,
            ];

            return $this->sendResponse($response, 'Location Point successfully updated!');
        }

        return $this->sendError('Location Point with pointId = ' . $locationId . ' not found');
    }

    public function deleteLocation($locationId)
    {
        if(is_null($locationId) || $locationId <= 0) {
            return $this->sendError('Incorrect pointId');
        }

        $location = LocationPoint::find($locationId);

        if(isset($location) && $location->animals->count() > 0) {
            return $this->sendError('You can\'t delete the location because it has animals!');
        }

        if($location) {
            $location->delete();

            return $this->sendResponse([],'Location Point was successfully deleted!');
        }

        return $this->sendError('Location Point with pointId = ' . $locationId . ' not found!');
    }
}
