<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('governorates')) {
            return;
        }

        Schema::table('governorates', function (Blueprint $table) {
            if (!Schema::hasColumn('governorates', 'home_shipping_price')) {
                $table->decimal('home_shipping_price', 10, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('governorates', 'post_shipping_price')) {
                $table->decimal('post_shipping_price', 10, 2)->nullable()->after('home_shipping_price');
            }
        });

        // Seed new columns with existing price values when possible.
        DB::table('governorates')
            ->whereNull('home_shipping_price')
            ->update([
                'home_shipping_price' => DB::raw('price')
            ]);

        DB::table('governorates')
            ->whereNull('post_shipping_price')
            ->update([
                'post_shipping_price' => DB::raw('price')
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('governorates')) {
            return;
        }

        Schema::table('governorates', function (Blueprint $table) {
            if (Schema::hasColumn('governorates', 'post_shipping_price')) {
                $table->dropColumn('post_shipping_price');
            }

            if (Schema::hasColumn('governorates', 'home_shipping_price')) {
                $table->dropColumn('home_shipping_price');
            }
        });
    }
};
