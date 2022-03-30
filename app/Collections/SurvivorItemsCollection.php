<?php

namespace App\Collections;

use App\Enums\SurvivorStatus;
use App\Models\Survivor;
use Illuminate\Database\Eloquent\Collection;

class SurvivorItemsCollection extends Collection
{

    public function ownedByInfected()
    {
        return $this->filter(function ($survivorItem) {
            $survivor = Survivor::firstWhere('id', $survivorItem['survivor_id']);
            return $survivor->status === SurvivorStatus::INFECTED;
        });
    }

}