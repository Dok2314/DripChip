<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\LocationPoint;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnimalVisitLocationController extends BaseApiController
{
    public function createVisitedLocation($animalId, $locationPointId)
    {
        if(is_null($animalId) || $animalId <= 0) {
            return $this->sendError('Incorrect animalId',[],400);
        }

        if(is_null($locationPointId) || $locationPointId <= 0) {
            return $this->sendError('Incorrect pointId',[],400);
        }

        $animal = Animal::find($animalId);

        if(is_null($animal)) {
            return $this->sendError('Animal with id = ' . $animalId . ' not found!');
        }

        if($animal->lifeStatus == 'DEAD') {
            return $this->sendError('Animal has lifeStatus = DEAD!', [], 400);
        }

        $locationPoint = LocationPoint::find($locationPointId);

        if(is_null($locationPoint)) {
            return $this->sendError('Location point with id = ' . $locationPointId . ' not found!');
        }

        if($animal->location_point_id == $locationPoint->id) {
            return $this->sendError('The animal is at the chipping point and has not moved anywhere, an attempt to add a location point equal to the chipping point!',[],400);
        }

        if($animal->visitedLocations->contains($locationPoint)) {
            return $this->sendError('Attempting to add a location point that already has an animal!', [],400);
        }

        $animal->visitedLocations()->syncWithoutDetaching([
            $locationPoint->id => [
                'startDateTime' => now(),
                'endDateTime' => now()->addYears(rand(1, 10)),
            ]
        ]);

        $lastId = DB::getPdo()->lastInsertId();

        $lastAddedVisitLocations = DB::table('animal_visited_locations')->where('id', $lastId)->first();
        $startDateTime = Carbon::parse($lastAddedVisitLocations->startDateTime)->format('Y-m-d\TH:i:sO');
        $endDateTime = Carbon::parse($lastAddedVisitLocations->endDateTime)->format('Y-m-d\TH:i:sO');

        $dateTimeOfVisitLocationPoint = $startDateTime . ' - ' . $endDateTime;

        $response = [
            'id' => $lastId,
            'dateTimeOfVisitLocationPoint' => $dateTimeOfVisitLocationPoint,
            'locationPointId' => $locationPoint->id,
        ];

        return $this->sendResponse($response,'Animal visit location successfully created!', 201);
    }

    public function getVisitedLocations($animalId, Request $request)
    {
        if(is_null($animalId) || $animalId <= 0) {
            return $this->sendError('Incorrect animalId',[], 400);
        }

        if($request->from < 0) {
            return $this->sendError('Incorrect from',[], 400);
        }

        if($request->size <= 0) {
            return $this->sendError('Incorrect size',[], 400);
        }

        if($request->startDateTime) {
            return $this->sendError('Incorrect format startDateTime',[], 400);
        }

        if($request->endDateTime) {
            return $this->sendError('Incorrect format endDateTime',[], 400);
        }

        $animal = Animal::find($animalId);

        if(is_null($animal)) {
            return $this->sendError('Animal with id = ' . $animalId . ' not found!');
        }

        // TODO: доделать

        $response = [
            'id' => '',
            'dateTimeOfVisitLocationPoint' => '',
            'locationPointId' => '',
        ];

        return $this->sendResponse($response, 'Successfully received visited location by animal!');
    }

    public function updateVisitedLocations($animalId, Request $request)
    {
        if(is_null($animalId) || $animalId <= 0) {
            return $this->sendError('Incorrect animalId', [], 400);
        }

        if(is_null($request->visitedLocationPointId) || $request->visitedLocationPointId <= 0) {
            return $this->sendError('Incorrect visitedLocationPointId', [], 400);
        }

        if(is_null($request->locationPointId) || $request->locationPointId <= 0) {
            return $this->sendError('Incorrect locationPointId', [], 400);
        }

        // TODO:

        $response = [
            'id' => '',
            'dateTimeOfVisitLocationPoint' => '',
            'locationPointId' => '',
        ];
    }

    public function deleteVisitedLocation($animalId, $visitedPointId)
    {
        if(is_null($animalId) || $animalId <= 0) {
            return $this->sendError('Incorrect animalId', [], 400);
        }

        if(is_null($visitedPointId) || $visitedPointId <= 0) {
            return $this->sendError('Incorrect visitedPointId', [], 400);
        }

        $animal = Animal::find($animalId);

        if(is_null($animal)) {
            return $this->sendError('Animal with id = ' . $animalId . ' not found!');
        }

        $locationPoint = LocationPoint::find($visitedPointId);

        if(is_null($locationPoint)) {
            return $this->sendError('An object with information about the visited location point with visitedPointId was not found!');
        }

        if(!$animal->visitedLocations->contains($locationPoint)) {
            return $this->sendError('The animal does not have an object with information about the visited location point with visitedPointId!');
        }

        $animal->visitedLocations()->detach($locationPoint);

        return $this->sendResponse([],'VisitedPoint by animal was successfully deleted!');
    }
}
