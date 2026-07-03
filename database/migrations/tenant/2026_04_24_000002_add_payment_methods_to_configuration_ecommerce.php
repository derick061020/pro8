<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodsToConfigurationEcommerce extends Migration
{
    public function up()
    {
            Schema::table('configuration_ecommerce', function (Blueprint $table) {
                $table->boolean('enable_yape')->default(false)->after('enable_store_pickup');
                $table->boolean('enable_transfer')->default(false)->after('enable_yape');
            });
    }

    public function down()
    {
        Schema::table('configuration_ecommerce', function (Blueprint $table) {
            $table->dropColumn(['enable_yape', 'enable_transfer']);
        });
    }
}
