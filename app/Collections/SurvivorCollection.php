<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class SurvivorCollection extends Collection
{

    public function filterByStatus(string $survivorStatus)
    {
        return $this->filter(function ($survivor) use ($survivorStatus) {
            return $survivor->status === $survivorStatus;
        });
    }

}