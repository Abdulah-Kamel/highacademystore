<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeeToShippingMethodsTable extends Migration
{
    public function up()
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            // add the fee field defaulting to 0.00
            $table->decimal('fee', 8, 2)
                  ->default(0)
                  ->after('phones');
        });
    }

    public function down()
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->dropColumn('fee');
        });
    }
}
