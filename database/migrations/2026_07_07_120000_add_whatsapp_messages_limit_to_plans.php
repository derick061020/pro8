<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWhatsappMessagesLimitToPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('whatsapp_messages_limit')->default(0)->after('sales_unlimited');
            $table->boolean('whatsapp_messages_unlimited')->default(true)->after('whatsapp_messages_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('whatsapp_messages_limit');
            $table->dropColumn('whatsapp_messages_unlimited');
        });
    }
}
