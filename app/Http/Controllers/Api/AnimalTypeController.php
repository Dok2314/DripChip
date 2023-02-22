<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnimalTypeRequest;
use App\Models\AnimalType;
use Illuminate\Http\Request;

class AnimalTypeController extends BaseApiController
{
    public function getInfo($animalTypeId)
    {
        if(is_null($animalTypeId) || $animalTypeId <= 0) {
            return $this->sendError('Incorrect type id',[],400);
        }

        $animalType = AnimalType::find($animalTypeId);

        if($animalType) {
            $response = [
                'id' => $animalType->id,
                'type' => $animalType->type,
            ];

            return $this->sendResponse($response, 'Animal Type successfully received!');
        }

        return $this->sendError('Animal Type with type id = ' . $animalTypeId . ' not found!');
    }

    public function createAnimalType(AnimalTypeRequest $request)
    {
        if(is_null($request->type)) {
            return $this->sendError('Incorrect type!',[], 400);
        }

        $existAnimalType = AnimalType::where('type', $request->type)->first();

        if($existAnimalType) {
            return $this->sendError('Animal type already exist!',[],409);
        }

        $animalType = AnimalType::create([
            'type' => $request->type,
        ]);

        $response = [
            'id' => $animalType->id,
            'type' => $animalType->type,
        ];

        return $this->sendResponse($response, 'Animal type successfully created!', 201);
    }

    public function updateAnimalType($animalTypeId, AnimalTypeRequest $request)
    {
        if(is_null($animalTypeId) || $animalTypeId <= 0) {
            return $this->sendError('Incorrect animal Type Id',[],400);
        }

        $animalType = AnimalType::find($animalTypeId);

        if(!$animalType) {
            return $this->sendError('Animal Type with id = ' . $animalTypeId . ' not found!');
        }

        $existAnimalType = AnimalType::where('type', $request->type)->first();

        if(isset($existAnimalType) && $existAnimalType->id != $animalTypeId) {
            return $this->sendError(
                sprintf(
                    'Animal Type with type = %s already exists!',
                    $request->type
                ),[],409);
        }

        $animalType->update([
            'type' => $request->type,
        ]);

        $response = [
            'id' => $animalType->id,
            'type' => $animalType->type,
        ];

        return $this->sendResponse($response, 'Animal Type successfully updated!');
    }

    public function deleteAnimalType($animalTypeId)
    {
        if(is_null($animalTypeId) || $animalTypeId <= 0) {
            return $this->sendError('Incorrect typeId!',[],400);
        }

        $animalType = AnimalType::find($animalTypeId);

        if(!$animalType) {
            return $this->sendError('Animal Type with id = ' . $animalTypeId . ' not found!');
        }

        if($animalType->animals->count() > 0) {
            return $this->sendError('This type has animals!',[],400);
        }

        $animalType->delete();

        return $this->sendResponse([],'Animal Type successfully deleted!');
    }
}
