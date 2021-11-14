<?php

namespace App\Http\Controllers;

use App\Enums\SurvivorStatus;
use App\Models\Item;
use App\Models\SurvivorItems;
use App\Services\ItemService;
use App\Services\SurvivorService;
use App\Utils\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    protected SurvivorService $survivorService;
    protected ItemService $itemService;

    public function __construct(SurvivorService $survivorService, ItemService $itemService)
    {
        $this->survivorService = $survivorService;
        $this->itemService = $itemService;
    }

    public function generateReport()
    {
        try {
            $allSurvivors = $this->survivorService->getAllSurvivors()->get();
            $infectedSurvivors = $allSurvivors->filterByStatus(SurvivorStatus::INFECTED);

            $allSurvivorsCount = $allSurvivors->count();
            if ($allSurvivorsCount === 0) {
                $infectedPercentage = 0;
                $cleanPercentage = 0;
            }
            else {
                $infectedSurvivorsCount = $infectedSurvivors->count();
                $infectedPercentage = round((($infectedSurvivorsCount/$allSurvivorsCount)*100), 1);

                $cleanSurvivorsCount = $allSurvivors->filterByStatus(SurvivorStatus::CLEAN)->count();
                $cleanPercentage = round((($cleanSurvivorsCount/$allSurvivorsCount)*100), 1);
            }

            $survivorsData = [
                'infectedPercentage' => $infectedPercentage,
                'cleanPercentage' => $cleanPercentage
            ];

            $items = $this->itemService->getAllItems()->get();

            $averagesOfItems = $items->mapWithKeys(function ($item) use ($allSurvivorsCount) {
                if ($allSurvivorsCount === 0)
                    $avgQuantityPerSurvivor = 0;
                else {
                    $totalItemQuantity = SurvivorItems::where('item_id', $item['id'])->sum('quantity');
                    $avgQuantityPerSurvivor = round(($totalItemQuantity/$allSurvivorsCount), 1);
                }

                return [$item['name'] => $avgQuantityPerSurvivor];
            });

            $infectedSurvivorItems = SurvivorItems::all()->ownedByInfected();
            $totalInfectedPoints = $infectedSurvivorItems->reduce(function ($carry, $survivorItem) {
                $item = Item::firstWhere('id', $survivorItem['item_id']);
                $carry += ($item->points * $survivorItem['quantity']);
                return $carry;
            }, 0);

            $itemsData = [
                'averagesOfItems' => $averagesOfItems,
                'totalInfectedPoints' => $totalInfectedPoints
            ];

            $data = [
                'survivors' => $survivorsData,
                'items' => $itemsData
            ];

            return ApiResponse::ofData($data);
        } catch (Exception $e) {
            Log::error("Error generating report: ", [$e]);
            return ApiResponse::ofInternalServerError("Error generating report");
        }
    }
}
