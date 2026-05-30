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
        $array = [];
        $initial = 5010;

        for ($i=0; $i <= 26 ; $i++) { 
            $array[] = $initial + $i;
        }
        DB::table('cat_attribute_types')
            ->whereNotIn(DB::raw('CAST(id AS CHAR)'), array_map('strval', $array))
            ->update(['active' => false]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('cat_attribute_types')
            ->update(['active' => true]);
    }
};
