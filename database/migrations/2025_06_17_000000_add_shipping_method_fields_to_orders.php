<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('shipping_method_id')->nullable()->after('near_post');
            $table->string('shipping_name')->nullable()->after('shipping_method_id');
            $table->string('shipping_address')->nullable()->after('shipping_name');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_method_id', 'shipping_name', 'shipping_address']);
        });
    }
};
