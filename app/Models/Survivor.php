<?php

namespace App\Models;

use App\Collections\SurvivorCollection;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survivor extends Model
{
    use HasFactory;
    use SpatialTrait;
    use SoftDeletes;

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

    public function flagLogs()
    {
        return $this->hasMany(SurvivorFlagLog::class, 'flag_origin');
    }

    public function newCollection(array $models = [])
    {
        return new SurvivorCollection($models);
    }
}
