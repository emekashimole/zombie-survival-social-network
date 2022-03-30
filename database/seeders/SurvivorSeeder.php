<?php

namespace Database\Seeders;

use App\Enums\SurvivorGender;
use App\Models\Item;
use App\Models\Survivor;
use App\Models\SurvivorItems;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class SurvivorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = Item::all();

        $survivors = Survivor::factory()
            ->count(5)
            ->state(new Sequence(
                ['gender' => SurvivorGender::MALE],
                ['gender' => SurvivorGender::FEMALE]
            ))
            ->create();

        foreach ($survivors as $survivor) {
            $survivorItems = $items->random(2);
            foreach ($survivorItems as $survivorItem) {
                SurvivorItems::create([
                    'survivor_id' => $survivor->id,
                    'item_id' => $survivorItem->id,
                    'quantity' => rand(1, 10)
                ]);
            }
        }
    }
}
