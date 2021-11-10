<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'points'
    ];

    public function ownedBy()
    {
        return $this->belongsToMany(Survivor::class, 'survivor_items');
    }
}
