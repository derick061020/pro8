<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetPrice1LabelDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Set default value for price1_label when null
        DB::table('configurations')
            ->update(['price1_label' => 'Precio principal']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
