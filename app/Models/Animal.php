<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $table = 'animals';

    public function types()
    {
        return $this->belongsToMany(AnimalType::class,'animal_type', 'type_id', 'animal_id');
    }
}
