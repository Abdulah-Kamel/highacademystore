<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->after('status')->default('شحن لاقرب مكتب بريد');
            }

            if (Schema::hasColumn('orders', 'is_fastDelivery')) {
                $table->dropColumn('is_fastDelivery');
            }
            if (Schema::hasColumn('orders', 'fast_ship')) {
                $table->dropColumn('fast_ship');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_method')) {
                $table->dropColumn('shipping_method');
            }
            if (!Schema::hasColumn('orders', 'is_fastDelivery')) {
                $table->boolean('is_fastDelivery')->default(0);
            }
        });
    }
};
