<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvivorInfectionFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'survivor_id',
        'count',
        'last_location'
    ];

    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }
}
