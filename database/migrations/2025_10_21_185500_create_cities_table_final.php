<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTableFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop cities table if it exists
        Schema::dropIfExists('cities');

        // Create cities table that matches the governorates ID type
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            
            // Use integer to match the existing governorates table ID type
            $table->integer('governorate_id')->unsigned();
            
            $table->string('name_ar');
            $table->string('name_en');
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Add index but no foreign key constraint to avoid compatibility issues
            $table->index(['governorate_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
