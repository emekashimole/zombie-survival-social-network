<?php

namespace App\Models;

use App\Collections\SurvivorItemsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvivorItems extends Model
{
    use HasFactory;

    protected $table = 'survivor_items';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'survivor_id',
        'item_id',
        'quantity'
    ];

    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function newCollection(array $models = [])
    {
        return new SurvivorItemsCollection($models);
    }
}
