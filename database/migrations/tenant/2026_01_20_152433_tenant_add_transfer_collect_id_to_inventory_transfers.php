<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        Schema::table('inventories_transfer', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['warehouse_destination_id']);
        });
        Schema::table('inventories_transfer', function (Blueprint $table) {
            $table->unsignedInteger("transfer_collect_id")->nullable()->after('warehouse_destination_id');
            $table->foreign('transfer_collect_id')->references('id')->on('inventories_transfer');
            $table->unsignedInteger('warehouse_id')->nullable()->change();
            $table->unsignedInteger('warehouse_destination_id')->nullable()->change();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories_transfer', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['warehouse_destination_id']);
        });
    
        Schema::table('inventories_transfer', function (Blueprint $table) {
            $table->dropForeign(['transfer_collect_id']);
            $table->dropColumn('transfer_collect_id');
            $table->unsignedInteger('warehouse_destination_id')->nullable(false)->change();
            $table->unsignedInteger('warehouse_id')->nullable(false)->change();
        });

    }
};
