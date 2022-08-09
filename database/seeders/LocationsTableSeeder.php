<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locationsArr = [
            [
                'name' => 'Jumeirah 2',
                'code' => 'DBJ2RS'
            ],
            [
                'name' => 'Marina',
                'code' => 'DBMAR'
            ],
            [
                'name' => 'Dubai Mall Zabeel',
                'code' => 'DBZAB'
            ],
        ];

        foreach($locationsArr as $location) {
            $createLocation = new \App\Models\Location($location);
            $createLocation->save();
        }
    }
}
