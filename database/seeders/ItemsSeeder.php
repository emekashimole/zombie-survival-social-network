<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            0 => [
                'name' => 'Water',
                'points' => 4
            ],
            1 => [
                'name' => 'Food',
                'points' => 3
            ],
            2 => [
                'name' => 'Medication',
                'points' => 2
            ],
            3 => [
                'name' => 'Ammunition',
                'points' => 1
            ]
        ];

        foreach ($items as $item) {
            Item::create([
                'name' => $item['name'],
                'points' => $item['points']
            ]);
        }
    }
}
