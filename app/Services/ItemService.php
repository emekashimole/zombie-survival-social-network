<?php

namespace App\Services;

use App\Exceptions\ActionNotAllowedException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Item;
use App\Models\Survivor;
use App\Models\SurvivorItems;

class ItemService
{

    public function getAllItems()
    {
        return Item::orderBy('name', 'asc');
    }

    public function addItem(array $itemInfo): Item
    {
        return Item::create([
            'name' => $itemInfo['name'],
            'points' => $itemInfo['points']
        ]);
    }

    public function updateItem(Item $item, array $itemInfo): Item
    {
        if (isset($itemInfo['points'])) $item->points = $itemInfo['points'];

        $item->save();

        return $item;
    }

    public function deleteItem(Item $item): void
    {
        $item->survivorItems()->delete();
        $item->delete();
    }

    public function itemOwnershipCheck(array $items, Survivor $survivor): void
    {
        $survivorItems = $survivor->survivorItems;
        if (!$survivorItems)
            throw new ResourceNotFoundException("No items found for Survivor: ".$survivor->name);

        foreach ($items as $item) {
            $check = $survivorItems->contains(function ($survivorItem) use ($item) {
                return ($survivorItem->item_id === $item['id'] && $survivorItem->quantity >= $item['quantity']);
            });
            if (!$check)
                throw new ActionNotAllowedException("Invalid item information entered for Survivor: ".$survivor->name);
        }
    }

    public function samePointsValueCheck(array $survivorItems, array $tradeSurvivorItems): bool
    {
        $survivorItemsTotalValue = array_reduce($survivorItems, function ($carry, $survivorItem) {
            $item = Item::firstWhere('id', $survivorItem['item_id']);
            $carry += ($item->points * $survivorItem['quantity']);
            return $carry;
        });

        $tradeSurvivorItemsTotalValue = array_reduce($tradeSurvivorItems, function ($carry, $tradeSurvivorItem) {
            $item = Item::firstWhere('id', $tradeSurvivorItem['item_id']);
            $carry += ($item->points * $tradeSurvivorItem['quantity']);
            return $carry;
        });

        return $survivorItemsTotalValue === $tradeSurvivorItemsTotalValue;
    }

    public function tradeItems(Survivor $survivor, array $survivorItems, Survivor $tradeSurvivor, array $tradeSurvivorItems): void
    {
        foreach ($survivorItems as $item) {
            $survivorItem = $survivor->survivorItems()->firstWhere('item_id', $item['id']);
            $survivorItem->quantity -= $item['quantity'];
            $survivorItem->save();
            
            $tradeSurvivorItem = $tradeSurvivor->survivorItems()->firstWhere('item_id', $item['id']);
            if (!$tradeSurvivorItem) {
                SurvivorItems::create([
                    'survivor_id' => $tradeSurvivor->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
            else {
                $tradeSurvivorItem->quantity += $item['quantity'];
                $tradeSurvivorItem->save();
            }
        }

        foreach ($tradeSurvivorItems as $item) {
            $tradeSurvivorItem = $tradeSurvivor->survivorItems()->firstWhere('item_id', $item['id']);
            $tradeSurvivorItem->quantity -= $item['quantity'];
            $tradeSurvivorItem->save();
            
            $survivorItem = $tradeSurvivor->survivorItems()->firstWhere('item_id', $item['id']);
            if (!$survivorItem) {
                SurvivorItems::create([
                    'survivor_id' => $survivor->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
            else {
                $survivorItem->quantity += $item['quantity'];
                $survivorItem->save();
            }
        }
    }

}