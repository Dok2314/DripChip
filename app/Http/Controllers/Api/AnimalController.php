<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnimalRequest;
use App\Models\Animal;
use Carbon\Carbon;
use Faker\Provider\Base;
use Illuminate\Http\Request;

class AnimalController extends BaseApiController
{
    const GENDERS = [
        "MALE",
        "FEMALE",
        "OTHER",
    ];

    public function getInfo($animalId)
    {
        if(is_null($animalId) || $animalId <= 0) {
            return $this->sendError('Incorrect animalId',[],400);
        }

        $animal = Animal::find($animalId);

        if($animal) {
            $response = [
                'id' => $animal->id,
                'animalTypes' => $animal->types->pluck('id'),
                'weight' => $animal->weight,
                'length' => $animal->length,
                'height' => $animal->height,
                'gender' => $animal->gender,
                'lifeStatus' => $animal->lifeStatus,
                'chippingDateTime' => Carbon::parse($animal->chippingDateTime)->format('Y-m-d\TH:i:sO'),
                'chipperId' => $animal->account->id,
                'chippingLocationId' => $animal->location_point_id,
                'visitedLocations' => $animal->visitedLocations->pluck('id'),
                'deathDateTime' => $animal->deathDateTime ? Carbon::parse($animal->deathDateTime)->format('Y-m-d\TH:i:sO') : '',
            ];

            return $this->sendResponse($response,'Animal successfully received!');
        }

        return $this->sendError('Animal with id = ' . $animalId . ' not found!');
    }

    public function searchAnimal(Request $request)
    {

    }

    public function createAnimal(AnimalRequest $request)
    {
        if(is_null($request->animalTypes) || count($request->animalTypes) <= 0) {
            return $this->sendError('Incorrect animalTypes!',[],400);
        }

        $animalTypes = $request->animalTypes;
        $uniqueAnimalTypes = array_unique($animalTypes);

        if (count($animalTypes) === count($uniqueAnimalTypes)) {
            foreach ($animalTypes as $animalType) {
                if(is_null($animalType) || $animalType <= 0) {
                    return $this->sendError('Incorrect animal type!',[],400);
                }
            }
        } else {
            return $this->sendError('Animal types are not unique!',[],400);
        }

        if(is_null($request->weight) || $request->weight <= 0) {
            return $this->sendError('Incorrect weight!',[],400);
        }

        if(is_null($request->length) || $request->length <= 0) {
            return $this->sendError('Incorrect length!',[],400);
        }

        if(is_null($request->height) || $request->height <= 0) {
            return $this->sendError('Incorrect height!',[],400);
        }

        if(is_null($request->gender) || !in_array($request->gender, self::GENDERS)) {
            return $this->sendError('Incorrect gender!',[],400);
        }

        if(is_null($request->chipperId) || $request->chipperId <= 0) {
            return $this->sendError('Incorrect chipperId!',[],400);
        }

        if(is_null($request->chippingLocationId) || $request->chippingLocationId <= 0) {
            return $this->sendError('Incorrect chippingLocationId!',[],400);
        }
    }
}
