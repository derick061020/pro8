<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedInteger('user_rel_subscription_plan_id')->nullable()->default(0)
                ->comment('Relacion con suscripciones');
            $table->unsignedInteger('collect_api_state_id')->default(99)
                ->comment('estado de api en global factoring');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'user_rel_subscription_plan_id',
                'collect_api_state_id',
            ]);
        });
    }
};
