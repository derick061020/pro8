<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        if (!Schema::hasTable('inventory_configurations')) {
            return;
        }

        if (!Schema::hasColumn('inventory_configurations', 'generate_internal_id')) {
            return;
        }

        DB::table('inventory_configurations')
            ->where('generate_internal_id', false)
            ->update(['generate_internal_id' => true]);

        Schema::table('inventory_configurations', function (Blueprint $table) {
            $table->boolean('generate_internal_id')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('inventory_configurations')) {
            return;
        }

        if (!Schema::hasColumn('inventory_configurations', 'generate_internal_id')) {
            return;
        }

        Schema::table('inventory_configurations', function (Blueprint $table) {
            $table->boolean('generate_internal_id')->default(false)->change();
        });
    }
};
