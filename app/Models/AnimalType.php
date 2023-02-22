<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalType extends Model
{
    use HasFactory;

    protected $table = 'animal_types';

    protected $fillable = [
        'type',
    ];

    public function animals()
    {
        return $this->belongsToMany(Animal::class,'animal_type','type_id', 'animal_id');
    }
}
