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
        // Integración de checkout con izipay para back office vendedor
        Schema::table('payment_configurations', function (Blueprint $table) {
            $table->boolean('enabled_izipay')->default(false)->after('enabled_culqi');
            $table->string('username_izipay')->nullable()->after('rsa_culqi');
            $table->string('password_izipay')->nullable()->after('username_izipay');
            $table->string('publickey_izipay')->nullable()->after('password_izipay');
            $table->text('sha256key_izipay')->nullable()->after('publickey_izipay');
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
                'username_izipay',
                'password_izipay',
                'publickey_izipay',
                'enabled_izipay',
                'sha256key_izipay'
            ]);
        });
    }
};
