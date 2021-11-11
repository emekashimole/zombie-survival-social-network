<?php

namespace App\Services;

use App\Enums\SurvivorStatus;
use App\Models\Survivor;
use App\Models\SurvivorItems;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;

class SurvivorService
{

    public function getAllSurvivors()
    {
        return Survivor::orderBy('name', 'asc');
    }

    public function getSurvivorById(int $id): ?Survivor
    {
        return Survivor::firstWhere('id', $id);
    }

    public function addSurvivor(array $survivorInfo): Survivor
    {
        $survivor = new Survivor();

        $survivor->name = $survivorInfo['name'];
        $survivor->age = $survivorInfo['age'];
        $survivor->gender = $survivorInfo['gender'];
        $survivor->last_location = new Point($survivorInfo['lastLocation']['lat'], $survivorInfo['lastLocation']['long']);
        $survivor->status = SurvivorStatus::CLEAN;

        $survivor->save();

        return $survivor;
    }

    public function updateSurvivor(Survivor $survivor, array $survivorInfo): Survivor
    {
        if (isset($survivorInfo['name'])) $survivor->name = $survivorInfo['name'];
        if (isset($survivorInfo['age'])) $survivor->age = $survivorInfo['age'];
        if (isset($survivorInfo['gender'])) $survivor->gender = $survivorInfo['gender'];
        if (isset($survivorInfo['lastLocation'])) $survivor->last_location = new Point($survivorInfo['lastLocation']['lat'], $survivorInfo['lastLocation']['long']);
        if (isset($survivorInfo['status'])) $survivor->status = $survivorInfo['status'];

        $survivor->save();

        return $survivor;
    }

    public function registerSurvivorItems(Survivor $survivor, array $items)
    {
        foreach ($items as $item) {
            SurvivorItems::create([
                'survivor_id' => $survivor->id,
                'item_id' => $item['id'],
                'quantity' => $item['quantity']
            ]);
        }
    }

    public function checkIfSurvivorIsClean(Survivor $survivor): bool
    {
        return ($survivor->status === SurvivorStatus::CLEAN);
    }

    public function checkIfSurvivorIsInfected(Survivor $survivor): bool
    {
        return ($survivor->status === SurvivorStatus::INFECTED);
    }

    public function deleteSurvivor(Survivor $survivor): void
    {
        $survivor->infectionFlag()->delete();
        $survivor->itemsOwned()->delete();
        $survivor->delete();
    }

}