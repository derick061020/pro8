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
        if (!Schema::hasTable('ejb_report_configurations')) {
            Schema::create('ejb_report_configurations', function(Blueprint $table) {
                $table->id();
                $table->string('document_type_id');
                $table->foreign('document_type_id')->references('id')->on('cat_document_types');
                $table->unsignedInteger('bank_account_pen_id')->nullable();
                $table->foreign('bank_account_pen_id')->references('id')->on('bank_accounts');
                $table->unsignedInteger('bank_account_usd_id')->nullable();
                $table->foreign('bank_account_usd_id')->references('id')->on('bank_accounts');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ejb_report_configurations');
    }
};
