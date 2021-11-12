<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvivorFlagLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'flag_origin',
        'flagged_survivor'
    ];
}
