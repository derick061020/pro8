<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTermsToSystemConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            // disabled | content | url  (mutuamente excluyentes por diseño)
            $table->string('terms_mode')->default('disabled');
            $table->longText('terms_content')->nullable();
            $table->string('terms_url')->nullable();
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['terms_mode', 'terms_content', 'terms_url']);
        });
    }
}
