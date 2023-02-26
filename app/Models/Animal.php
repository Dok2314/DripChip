<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
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

    use HasFactory;

    protected $table = 'animals';

//    protected $casts = [
//        'chippingDateTime' => 'datetime',
//        'deathDateTime' => 'datetime',
//    ];

    protected $fillable = [
        'weight', 'length', 'height', 'gender', 'lifeStatus', 'chipperId',
        'location_point_id', 'chippingDateTime', 'deathDateTime',
    ];

    public function types()
    {
        return $this->belongsToMany(AnimalType::class,'animal_type', 'animal_id', 'type_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class,'chipperId');
    }

    public function visitedLocations()
    {
        return $this->belongsToMany(LocationPoint::class, 'animal_visited_locations', 'animal_id','location_point_id');
    }

    public static function getGenders(): array
    {
        return self::GENDERS;
    }

    public static function getLifeStatuses(): array
    {
        return self::LIFE_STATUSES;
    }
}
