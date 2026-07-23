<?php

use Illuminate\Database\Migrations\Migration;

class TenantAddItinerantOperationTypeToCatOperationTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cat_operation_types')->updateOrInsert(
            ['id' => '0101_itinerant'],
            [
                'active' => false,
                'exportation' => false,
                'description' => 'Venta Interna - Itinerante',
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('cat_operation_types')->where('id', '0101_itinerant')->delete();
    }
}
