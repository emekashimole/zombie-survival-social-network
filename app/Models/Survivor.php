<?php

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survivor extends Model
{
    use HasFactory;
    use SpatialTrait;

    protected $fillable = [
        'name',
        'age',
        'gender',
        'last_location',
        'status'
    ];

    protected $spatialFields = [
        'last_location'
    ];

    public function itemsOwned()
    {
        return $this->belongsToMany(Item::class, 'survivor_items');
    }

    public function survivorItems()
    {
        return $this->hasMany(SurvivorItems::class);
    }

    public function infectionFlag()
    {
        return $this->hasOne(SurvivorInfectionFlag::class);
    }
}
