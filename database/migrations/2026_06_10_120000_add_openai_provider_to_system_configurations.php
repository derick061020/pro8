<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenaiProviderToSystemConfigurations extends Migration
{
    public function up()
    {
        Schema::connection('system')->table('configurations', function (Blueprint $table) {
            // El proveedor de IA se ofrece desde el system (panel reseller).
            // Los tenants ya no configuran su propia key — heredan estos
            // valores. Por eso movemos los campos del tenant a system.
            $table->string('openai_api_key', 255)->nullable()->after('apk_url');
            $table->string('openai_model', 100)->nullable()->after('openai_api_key');
        });
    }

    public function down()
    {
        Schema::connection('system')->table('configurations', function (Blueprint $table) {
            $table->dropColumn(['openai_api_key', 'openai_model']);
        });
    }
}
