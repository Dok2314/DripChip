<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnimalRequest;
use App\Http\Requests\Api\AnimalUpdateRequest;
use App\Models\Account;
use App\Models\Animal;
use App\Models\AnimalType;
use App\Models\LocationPoint;
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

    const LIFE_STATUSES = [
        "ALIVE",
        "DEAD",
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
                'animalTypes' => $animal->types->pluck('id')->toArray(),
                'weight' => $animal->weight,
                'length' => $animal->length,
                'height' => $animal->height,
                'gender' => $animal->gender,
                'lifeStatus' => $animal->lifeStatus,
                'chippingDateTime' => $animal->chippingDateTime ? Carbon::parse($animal->chippingDateTime)->format('Y-m-d\TH:i:sO') : '',
                'chipperId' => $animal->account?->id,
                'chippingLocationId' => $animal->location_point_id,
                'visitedLocations' => $animal->visitedLocations->pluck('id')->toArray(),
                'deathDateTime' => $animal->deathDateTime ? Carbon::parse($animal->deathDateTime)->format('Y-m-d\TH:i:sO') : '',
            ];

            return $this->sendResponse($response,'Animal successfully received!');
        }

        return $this->sendError('Animal with id = ' . $animalId . ' not found!');
    }

    public function searchAnimal(Request $request)
    {
        dd('test');
    }

    public function createAnimal(AnimalRequest $request)
    {
        if(is_null($request->animalTypes) || count($request->animalTypes) <= 0) {
            return $this->sendError('Incorrect animalTypes!',[],400);
        }

        $animalTypes = $request->animalTypes;
        $uniqueAnimalTypes = array_unique($animalTypes);
        $animalTypesIds = AnimalType::pluck('id');

        if (count($animalTypes) === count($uniqueAnimalTypes)) {
            foreach ($animalTypes as $animalType) {
                if(is_null($animalType) || $animalType <= 0) {
                    return $this->sendError('Incorrect animal type!',[],400);
                }

                if(!in_array($animalType, $animalTypesIds->toArray())) {
                    return $this->sendError('Animal type not found!', [], 400);
                }
            }
        } else {
            return $this->sendError('AnimalTypes array contains duplicates!',[],409);
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

        $account = Account::where('user_id', $request->chipperId)->first();

        if(is_null($account)) {
            return $this->sendError('Account with chipperId = ' . $request->chipperId . ' not found!',[],400);
        }

        $locationPoint = LocationPoint::find($request->chippingLocationId);

        if(is_null($locationPoint)) {
            return $this->sendError('Location point with chippingLocationId = ' . $request->chippingLocationId . ' not found!',[],400);
        }

        $animal = Animal::create([
            'weight' => $request->weight,
            'length' => $request->length,
            'height' => $request->height,
            'gender' => $request->gender,
            'chipperId' => $account->id,
            'location_point_id' => $request->chippingLocationId,
            'chippingDateTime' => now(),
            'lifeStatus' => 'ALIVE',
        ]);

        $locationPointIds = LocationPoint::pluck('id')->toArray();
        $animal->types()->sync($animalTypes);

        $animal->visitedLocations()->syncWithPivotValues(
            $locationPointIds,
            [
                'startDateTime' => now(),
                'endDateTime' => now()->addYears(rand(1, 10)),
            ]
        );

        $response = [
            'id' => $animal->id,
            'animalTypes' => $animal->types->pluck('id')->toArray(),
            'weight' => $animal->weight,
            'length' => $animal->length,
            'height' => $animal->height,
            'gender' => $animal->gender,
            'lifeStatus' => $animal->lifeStatus,
            'chippingDateTime' => $animal->chippingDateTime ? Carbon::parse($animal->chippingDateTime)->format('Y-m-d\TH:i:sO') : '',
            'chipperId' => $animal->chipperId,
            'chippingLocationId' => $animal->location_point_id,
            'visitedLocations' => $animal->visitedLocations->pluck('id')->toArray(),
            'deathDateTime' => $animal->deathDateTime,
        ];

        return $this->sendResponse($response,'Animal successfully created!',201);
    }

    public function updateAnimal($animalId, AnimalUpdateRequest $request)
    {
        if(is_null($animalId) || $animalId <= 0) {
            return $this->sendError('Incorrect animalId',[],400);
        }

        if(is_null($request->weight) || $request->weight <= 0) {
            return $this->sendError('Incorrect weight',[],400);
        }

        if(is_null($request->length) || $request->length <= 0) {
            return $this->sendError('Incorrect length',[],400);
        }

        if(is_null($request->height) || $request->height <= 0) {
            return $this->sendError('Incorrect height',[],400);
        }

        if(!in_array($request->gender, self::GENDERS)) {
            return $this->sendError('Incorrect gender!',[],400);
        }

        if(!in_array($request->lifeStatus, self::LIFE_STATUSES)) {
            return $this->sendError('Incorrect lifeStatus!',[],400);
        }

        if(is_null($request->chipperId) || $request->chipperId <= 0) {
            return $this->sendError('Incorrect chipperId',[],400);
        }

        if(is_null($request->chippingLocationId) || $request->chippingLocationId <= 0) {
            return $this->sendError('Incorrect chippingLocationId',[],400);
        }

        $animal = Animal::find($animalId);

        if(is_null($animal)) {
            return $this->sendError('Animal with id = ' . $animalId . ' not found.',[],400);
        }

        $account = Account::find($request->chipperId);

        if(is_null($account)) {
            return $this->sendError('Account with id = ' . $request->chipperId . ' not found.',[],400);
        }

        $locationPoint = LocationPoint::find($animal->location_point_id);

        if(is_null($locationPoint)) {
            return $this->sendError('Location Point with id = ' . $animal->location_point_id . ' not found.',[],400);
        }

        if($animal->lifeStatus == 'DEAD' && $request->lifeStatus == 'ALIVE') {
            return $this->sendError('You cannot set this lifeStatus to an animal because it is dead.',[],400);
        }

        // TODO:
//        if() {
//            return $this->sendError('The new chipping point is the same as the first visited location point!',[],400);
//        }

        $animal->update([
            'weight' => $request->weight,
            'length' => $request->length,
            'height' => $request->height,
            'gender' => $request->gender,
            'lifeStatus' => $request->lifeStatus,
            'chipperId' => $request->chipperId,
            'chippingLocationId' => $request->chippingLocationId,
        ]);

        if($request->lifeStatus == 'DEAD' && $animal->lifeStatus == 'ALIVE') {
            $animal->deathDateTime = now();
            $animal->save();
        }

        $response = [
            'id' => $animal->id,
            'animalTypes' => $animal->types->pluck('id')->toArray(),
            'weight' => $animal->weight,
            'length' => $animal->length,
            'height' => $animal->height,
            'gender' => $animal->gender,
            'lifeStatus' => $animal->lifeStatus,
            'chippingDateTime' => $animal->chippingDateTime ? Carbon::parse($animal->chippingDateTime)->format('Y-m-d\TH:i:sO') : '',
            'chipperId' => $animal->chipperId,
            'chippingLocationId' => $animal->location_point_id,
            'visitedLocations' => $animal->visitedLocations->pluck('id')->toArray(),
            'deathDateTime' => $animal->deathDateTime ? Carbon::parse($animal->deathDateTime)->format('Y-m-d\TH:i:sO') : null,
        ];

        return $this->sendResponse($response,'Animal successfully updated!');
    }

    public function deleteAnimal($animalId)
    {
        if(is_null($animalId) || $animalId <= 0) {
            return $this->sendError('Incorrect animalId', [],400);
        }

        // TODO: Животное покинуло локацию чипирования, при этом есть другие посещенные точки

        $animal = Animal::find($animalId);

        if(is_null($animal)) {
            return $this->sendError('Animal with animalId = ' . $animalId . ' not found!');
        }

        $animal->delete();

        return $this->sendResponse([],'Animal successfully deleted!');
    }
}
