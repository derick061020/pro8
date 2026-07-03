<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModulePermissionsToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('system')->table('plans', function (Blueprint $table) {
            $table->json('module_permissions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('system')->table('plans', function (Blueprint $table) {
            $table->dropColumn('module_permissions');
        });
    }
}