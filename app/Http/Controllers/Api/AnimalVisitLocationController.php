<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\LocationPoint;
use Illuminate\Http\Request;

class AnimalVisitLocationController extends BaseApiController
{
    public function createVisitLocation($animalId, $locationPointId)
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

        $response = [
            'id' => $locationPoint->id,
            'dateTimeOfVisitLocationPoint' => '',
            'locationPointId' => $locationPoint->id,
        ];

        return $this->sendResponse($response,'Animal visit location successfully created!', 201);
    }


}
