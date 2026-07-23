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
        Schema::table('custom_fields', function (Blueprint $table) {
            if (!Schema::hasColumn('custom_fields', 'enabled_for_quotations')) {
                $table->boolean('enabled_for_quotations')->default(false)->after('enabled_for_order_notes');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'custom_fields_data')) {
                $table->json('custom_fields_data')->nullable()->after('subtotal');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            if (Schema::hasColumn('custom_fields', 'enabled_for_quotations')) {
                $table->dropColumn('enabled_for_quotations');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'custom_fields_data')) {
                $table->dropColumn('custom_fields_data');
            }
        });
    }
};
