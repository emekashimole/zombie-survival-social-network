<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survivor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'gender',
        'last_location',
        'status'
    ];

    public function itemsOwned()
    {
        return $this->belongsToMany(Item::class, 'survivor_items');
    }
}
