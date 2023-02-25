<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalVisitedLocations extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id',
        'location_point_id',
        'startDateTime',
        'endDateTime',
    ];
}
