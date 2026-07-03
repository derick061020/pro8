<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrintDestinationToRestaurantConfigurations extends Migration
{
    public function up()
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->boolean('print_destination')->default(true)->after('print_local_enabled')
                ->comment('false = Directo a BuhoPrinter local | true = Centralizado vía backend/Redis');
        });
    }

    public function down()
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->dropColumn('print_destination');
        });
    }
}
