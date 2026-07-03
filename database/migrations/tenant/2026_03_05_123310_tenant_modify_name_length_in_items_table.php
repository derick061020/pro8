<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasColumn('items', 'name')) {
            return;
        }

        if ($this->indexExists('items', 'items_name_index')) {
            DB::statement('ALTER TABLE items DROP INDEX items_name_index');
        }

        DB::statement('ALTER TABLE items CHANGE name name VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL');

        DB::statement('ALTER TABLE items ADD INDEX items_name_index (name(191))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('items', 'name')) {
            return;
        }

        if ($this->indexExists('items', 'items_name_index')) {
            DB::statement('ALTER TABLE items DROP INDEX items_name_index');
        }

        DB::statement('ALTER TABLE items CHANGE name name VARCHAR(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL');

        DB::statement('ALTER TABLE items ADD INDEX items_name_index (name)');
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return count($indexes) > 0;
    }
};
