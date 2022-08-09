<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = new \App\Models\Item([
            'name'  => 'item 1',
            'sku'   => 'sku 1',
            'price'   => '101.99',
        ]);
        $item->save();
        $item = new \App\Models\Item([
            'name'  => 'item 2',
            'sku'   => 'sku 2',
            'price'   => '100.00',
        ]);
        $item->save();
        $item = new \App\Models\Item([
            'name'  => 'item 3',
            'sku'   => 'sku 3',
            'price'   => '99.99',
        ]);
        $item->save();
    }
}
