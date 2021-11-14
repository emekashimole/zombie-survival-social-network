<?php

namespace App\Services;

use App\Enums\SurvivorStatus;
use App\Exceptions\OriginFlagAlreadyExistsException;
use App\Models\Survivor;
use App\Models\SurvivorFlagLog;
use App\Models\SurvivorInfectionFlag;
use App\Models\SurvivorItems;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function ifSurvivorIsClean(Survivor $survivor): bool
    {
        return ($survivor->status === SurvivorStatus::CLEAN);
    }

    public function ifSurvivorIsInfected(Survivor $survivor): bool
    {
        return ($survivor->status === SurvivorStatus::INFECTED);
    }

    public function deleteSurvivor(Survivor $survivor): void
    {
        $survivor->infectionFlag()->delete();
        $survivor->itemsOwned()->delete();
        $survivor->delete();
    }

    public function flagInfectedSurvivor(Survivor $survivor, array $flagInfo): int
    {
        $originFlagAlreadyExists = SurvivorFlagLog::where('flag_origin', $flagInfo['flagOriginId'])
            ->where('flagged_survivor', $survivor->id)
            ->exists();

        if ($originFlagAlreadyExists)
            throw new OriginFlagAlreadyExistsException();
        
        $survivorAlreadyFlagged = $survivor->infectionFlag()->exists();
        if ($survivorAlreadyFlagged) {
            $currentFlagCount = $survivor->infectionFlag->count;
            $survivor->infectionFlag()->update([
                'count' => $currentFlagCount+1
            ]);
            $survivor->refresh();
        }
        else {
            $survivorInfectionFlag = new SurvivorInfectionFlag();
            $survivorInfectionFlag->survivor_id = $survivor->id;
            $survivorInfectionFlag->count = 1;
            $survivorInfectionFlag->last_location = new Point($flagInfo['lastLocation']['lat'], $flagInfo['lastLocation']['long']);
            $survivorInfectionFlag->save();
        }

        SurvivorFlagLog::create([
            'flag_origin' => $flagInfo['flagOriginId'],
            'flagged_survivor' => $survivor->id
        ]);

        return $survivor->infectionFlag->count;
    }

}