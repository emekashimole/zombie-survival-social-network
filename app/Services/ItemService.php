<?php

namespace App\Services;

use App\Models\Item;

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

}