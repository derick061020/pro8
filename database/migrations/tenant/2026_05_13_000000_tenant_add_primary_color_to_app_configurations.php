<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TenantAddPrimaryColorToAppConfigurations extends Migration
{
    public function up()
    {
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->string('primary_color')->nullable()->after('direct_send_documents_whatsapp')->comment('Color principal para la nueva app movil - hexadecimal')->default('#020F3C');
        });
    }

    public function down()
    {
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->dropColumn('primary_color');
        });
    }
}
