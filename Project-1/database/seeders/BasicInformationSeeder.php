<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\Area;
use App\Models\Category;
use App\Models\Country;
use App\Models\Plane;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasicInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $areas=['Damascus','Aleppo','Latakia'];

        foreach ($areas as $area) {
            Area::create([
                'name'=> $area,
                'country_id'=>1
            ]);
        }

        $areas=['Paris'];

        foreach ($areas as $area) {
            Area::create([
                'name'=> $area,
                'country_id'=>2
            ]);
        }




        $areas=['Hamburg','Berlin'];

        foreach ($areas as $area) {
            Area::create([
                'name'=> $area,
                'country_id'=>3
            ]);
        }

        $categorise=['Islamic','Cultural','Historical','Natural','Restaurant','Entertaining'];

        foreach ($categorise as $category) {
            Category::create([
                'name'=> $category,
            ]);
        }

        $airports=['Syria A'];

        foreach ($airports as $airport) {
            Airport::create([
                'name'=> $airport,
                'user_id'=>5,
                'area_id'=>1,
                'country_id'=>1
            ]);
        }

        $airports=['France A'];
        foreach ($airports as $airport) {
            Airport::create([
                'name'=> $airport,
                'user_id'=>6,
                'area_id'=>4,
                'country_id'=>2
            ]);
        }

        $planes=['Damas A1','Damas A2'];

        foreach ($planes as $plane) {
            Plane::create([
                'name'=> $plane,
                'airport_id'=>1,
                'number_of_seats'=>40,
                'ticket_price'=>50
            ]);
        }
        $planes=['PARIS A1','PARIS A2'];

        foreach ($planes as $plane) {
            Plane::create([
                'name'=> $plane,
                'airport_id'=>2,
                'number_of_seats'=>40,
                'ticket_price'=>50
            ]);
        }

    }
}
