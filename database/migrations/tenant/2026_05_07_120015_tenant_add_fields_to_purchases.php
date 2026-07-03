<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('period')->nullable();
            $table->dateTime('date_registration')->nullable();
            $table->string('related_invoice_number')->nullable();
            $table->string('related_invoice_serie')->nullable();
            $table->string('affectation')->nullable();
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'period',
                'date_registration',
                'related_invoice_number',
                'related_invoice_serie',
                'affectation',
            ]);
        });
    }
};
