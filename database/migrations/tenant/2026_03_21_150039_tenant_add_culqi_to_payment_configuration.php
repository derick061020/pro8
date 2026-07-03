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
        Schema::table('payment_configurations', function (Blueprint $table) {
            $table->text('publickey_culqi')->nullable()->after('public_key_mp');
            $table->text('privatekey_culqi')->nullable()->after('publickey_culqi');
            $table->boolean('enabled_culqi')->default(false)->after('privatekey_culqi');
            $table->text('idrsa_culqi')->nullable()->after('enabled_culqi');
            $table->text('rsa_culqi')->nullable()->after('idrsa_culqi');
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
            $table->dropColumn('publickey_culqi');
            $table->dropColumn('privatekey_culqi');
            $table->dropColumn('rsa_culqi');
            $table->dropColumn('enabled_culqi');
            $table->dropColumn('idrsa_culqi');
        });
    }
};
