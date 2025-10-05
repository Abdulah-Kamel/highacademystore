<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToShippingMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_methods', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->enum('type', ['post','home','branch']);
            $t->unsignedBigInteger('government')->nullable();
            $t->string('address')->nullable();
            $t->json('phones')->nullable();
            $t->decimal('fee', 8, 2)->default(0);
            $t->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_methods');
    }
}
