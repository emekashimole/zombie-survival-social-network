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

    public function samePointsValueCheck(array $survivorAItems, array $survivorBItems): bool
    {
        $survivorAItemsTotalValue = array_reduce($survivorAItems, function ($carry, $survivorItem) {
            $item = Item::firstWhere('id', $survivorItem['id']);
            $carry += ($item->points * $survivorItem['quantity']);
            return $carry;
        });

        $survivorBItemsTotalValue = array_reduce($survivorBItems, function ($carry, $survivorItem) {
            $item = Item::firstWhere('id', $survivorItem['id']);
            $carry += ($item->points * $survivorItem['quantity']);
            return $carry;
        });

        return $survivorAItemsTotalValue === $survivorBItemsTotalValue;
    }

    public function tradeItems(Survivor $survivorA, array $survivorAItems, Survivor $survivorB, array $survivorBItems): void
    {
        foreach ($survivorAItems as $item) {
            $survivorAItem = SurvivorItems::where('survivor_id', $survivorA->id)
                ->where('item_id', $item['id'])->first();
            $survivorAItem->quantity -= $item['quantity'];
            $survivorAItem->save();
            if ($survivorAItem->quantity === 0)
                $survivorAItem->delete();
            
            $survivorBItem = SurvivorItems::where('survivor_id', $survivorB->id)
                ->where('item_id', $item['id'])->first();
            if (!$survivorBItem) {
                SurvivorItems::create([
                    'survivor_id' => $survivorB->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
            else {
                $survivorBItem->quantity += $item['quantity'];
                $survivorBItem->save();
            }
        }

        foreach ($survivorBItems as $item) {
            $survivorBItem = SurvivorItems::where('survivor_id', $survivorB->id)
                ->where('item_id', $item['id'])->first();
            $survivorBItem->quantity -= $item['quantity'];
            $survivorBItem->save();
            if ($survivorBItem->quantity === 0)
                $survivorBItem->delete();
            
            $survivorAItem = SurvivorItems::where('survivor_id', $survivorA->id)
                ->where('item_id', $item['id'])->first();
            if (!$survivorAItem) {
                SurvivorItems::create([
                    'survivor_id' => $survivorA->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
            else {
                $survivorAItem->quantity += $item['quantity'];
                $survivorAItem->save();
            }
        }
    }

}