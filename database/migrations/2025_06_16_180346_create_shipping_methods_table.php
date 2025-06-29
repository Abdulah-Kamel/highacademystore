<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');         // e.g. "استلام من المكتبه فرع شبين الكوم"
            $table->string('address');      // free-text address
            $table->unsignedInteger('government');  
              // matches the `id` in your JSON file, not a DB FK
            $table->json('phones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_methods');
    }
}
