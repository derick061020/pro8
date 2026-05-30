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
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('global_discount_type_id')->default('02')->change();
        });

        DB::table('configurations')
            ->whereNull('global_discount_type_id')
            ->orWhere('global_discount_type_id', '')
            ->update(['global_discount_type_id' => '02']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('global_discount_type_id')->default('03')->change();
        });
    }
};
