<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->char('document_type_id', 2)->default('');
            $table->unsignedInteger('user_rel_subscription_plan_id')->nullable()->default(0)
                ->comment('Relacion con suscripciones');
            $table->string('voided_description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->dropColumn([
                'document_type_id',
                'user_rel_subscription_plan_id',
                'voided_description',
            ]);
        });
    }
};
