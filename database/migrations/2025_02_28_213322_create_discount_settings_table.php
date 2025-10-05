<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDiscountSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_settings', function (Blueprint $table) {
            $table->id();
            // Global flag to enable/disable the discount feature
            $table->boolean('discount_enabled')->default(true);
            $table->timestamps();
        });

        // Optionally, insert a default setting
        DB::table('discount_settings')->insert([
            'discount_enabled' => true,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_settings');
    }
}
