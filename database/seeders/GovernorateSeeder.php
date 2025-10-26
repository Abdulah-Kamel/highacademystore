<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // Since the table already has data, let's just update it with the JSON data
        // Read JSON file
        $jsonPath = storage_path('cities/governorates.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('Governorates JSON file not found at: ' . $jsonPath);
            return;
        }

        $governorates = json_decode(File::get($jsonPath), true);

        foreach ($governorates as $governorate) {
            // Update existing records or insert new ones
            DB::table('governorates')->updateOrInsert(
                ['id' => $governorate['id']],
                [
                    'governorate_name_ar' => $governorate['governorate_name_ar'],
                    'governorate_name_en' => $governorate['governorate_name_en'],
                    'price' => $governorate['price'],
                    'home_shipping_price' => $governorate['home_shipping_price'] ?? $governorate['home_price'] ?? $governorate['price'],
                    'post_shipping_price' => $governorate['post_shipping_price'] ?? $governorate['post_price'] ?? $governorate['price'],
                ]
            );
        }

        $this->command->info('Governorates updated successfully from JSON file.');
    }
}
