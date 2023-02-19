<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
    ];

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }
}
