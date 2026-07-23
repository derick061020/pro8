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
        Schema::table('payment_configurations', function(Blueprint $table) {
            if (! Schema::hasColumn('payment_configurations', 'enabled_mp')) {
                $table->boolean('enabled_mp')->nullable();
            }
            if (! Schema::hasColumn('payment_configurations', 'access_token_mp')) {
                $table->string('access_token_mp')->nullable();
            }
            if (! Schema::hasColumn('payment_configurations', 'public_key_mp')) {
                $table->string('public_key_mp')->nullable();
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
        Schema::table('payment_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'enabled_mp',
                'access_token_mp',
                'public_key_mp'
            ]);
        });
    }
};
