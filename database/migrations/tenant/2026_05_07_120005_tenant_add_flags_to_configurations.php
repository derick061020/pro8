<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('disable_users')->default(false);
            $table->boolean('enable_bank_accounts')->default(false);
            $table->boolean('item_name_autocomplete')->default(false)
                ->comment('autocompleta nombre y codigo de barra con atributos');
            $table->boolean('search_by_click')->default(false);
            $table->boolean('disable_retention_for_amount')->default(false);
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn([
                'disable_users',
                'enable_bank_accounts',
                'item_name_autocomplete',
                'search_by_click',
                'disable_retention_for_amount',
            ]);
        });
    }
};
