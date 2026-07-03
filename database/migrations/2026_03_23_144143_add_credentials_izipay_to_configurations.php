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
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('enabled_izipay')->default(false);
            $table->boolean('enabled_culqi')->default(false);
            $table->string('username_izipay')->nullable();
            $table->string('password_izipay')->nullable();
            $table->string('publickey_izipay')->nullable();
            $table->text('sha256key_izipay')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn([
                'enabled_izipay',
                'enabled_culqi',
                'username_izipay',
                'password_izipay',
                'publickey_izipay',
                'sha256key_izipay'
            ]);
        });
    }
};
