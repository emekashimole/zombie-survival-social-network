<?php

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvivorInfectionFlag extends Model
{
    use HasFactory;
    use SpatialTrait;

    protected $fillable = [
        'survivor_id',
        'count',
        'last_location'
    ];

    protected $spatialFields = [
        'last_location'
    ];

    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }
}
