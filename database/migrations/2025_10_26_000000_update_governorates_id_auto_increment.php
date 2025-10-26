<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure the governorates table uses an auto-incrementing primary key.
     */
    public function up(): void
    {
        if (!Schema::hasTable('governorates')) {
            return;
        }

        $database = DB::getDatabaseName();

        $column = DB::selectOne("
            SELECT COLUMN_TYPE, EXTRA
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'governorates'
              AND COLUMN_NAME = 'id'
        ", [$database]);

        if (!$column || str_contains(strtolower($column->EXTRA ?? ''), 'auto_increment')) {
            return;
        }

        $columnType = strtoupper($column->COLUMN_TYPE);
        DB::statement("ALTER TABLE governorates MODIFY id {$columnType} NOT NULL AUTO_INCREMENT");
    }

    /**
     * Rollback the auto-increment change if needed.
     */
    public function down(): void
    {
        if (!Schema::hasTable('governorates')) {
            return;
        }

        $database = DB::getDatabaseName();

        $column = DB::selectOne("
            SELECT COLUMN_TYPE, EXTRA
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'governorates'
              AND COLUMN_NAME = 'id'
        ", [$database]);

        if (!$column || !str_contains(strtolower($column->EXTRA ?? ''), 'auto_increment')) {
            return;
        }

        $columnType = strtoupper($column->COLUMN_TYPE);

        // Remove AUTO_INCREMENT flag but keep the original type.
        DB::statement("ALTER TABLE governorates MODIFY id {$columnType} NOT NULL");
    }
};
