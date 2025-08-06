<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDiscountColumnsFromOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('offers', 'value')) {
                $table->dropColumn('value');
            }
            if (Schema::hasColumn('offers', 'minimum_books')) {
                $table->dropColumn('minimum_books');
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
        Schema::table('offers', function (Blueprint $table) {
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2)->default(0);
            $table->integer('minimum_books')->default(1);
        });
    }
}
