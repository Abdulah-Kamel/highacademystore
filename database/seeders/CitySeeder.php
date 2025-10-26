<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data
        City::truncate();

        // Read JSON file
        $jsonPath = storage_path('cities/cities.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error('Cities JSON file not found at: ' . $jsonPath);
            return;
        }

        $cities = json_decode(File::get($jsonPath), true);

        foreach ($cities as $city) {
            City::create([
                'governorate_id' => $city['governorate_id'],
                'name_ar' => $city['city_name_ar'],
                'name_en' => $city['city_name_en'],
                'status' => true
            ]);
        }

        $this->command->info('Cities seeded successfully from JSON file.');
    }
}
