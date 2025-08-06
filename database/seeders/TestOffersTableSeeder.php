<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestOffersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the offers table first
        DB::table('offers')->truncate();
        
        // Add a test offer
        Offer::create([
            'image' => 'test-offer.jpg', // Make sure this image exists in storage/app/public/images/offers/
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->command->info('Test offer added successfully!');
    }
}
