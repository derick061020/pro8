<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('pse_username')->nullable();
            $table->string('pse_password')->nullable();
            $table->text('pse_token')->nullable();
            $table->boolean('qr_api_enable_ws')->default(false);
            $table->string('qr_api_url_ws')->nullable();
            $table->string('qr_api_key_ws')->nullable();
            $table->string('security_code')->nullable();
            $table->string('name_person_support_contaweb')->nullable();
            $table->string('telephone_support_contaweb')->nullable();
            $table->string('email_support_contaweb')->nullable();
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'pse_username',
                'pse_password',
                'pse_token',
                'qr_api_enable_ws',
                'qr_api_url_ws',
                'qr_api_key_ws',
                'security_code',
                'name_person_support_contaweb',
                'telephone_support_contaweb',
                'email_support_contaweb',
            ]);
        });
    }
};
