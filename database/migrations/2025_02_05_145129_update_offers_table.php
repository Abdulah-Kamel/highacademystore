<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->enum('type', ['percentage', 'fixed'])->default('percentage'); // Type of discount
            $table->decimal('value', 8, 2)->default(0); // Discount value (percentage or fixed amount)
            $table->integer('minimum_books')->default(10); // Minimum number of books required for discount
        });
    }

    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['type', 'value', 'minimum_books']);
        });
    }
}
