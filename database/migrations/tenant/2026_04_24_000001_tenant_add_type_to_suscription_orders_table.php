<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('suscription_orders', function (Blueprint $table) {
            $table->enum('type', ['new_suscription', 'suscription_order'])
                  ->default('suscription_order')
                  ->after('suscription_id');
        
            $table->string('person_number', 25)->nullable()->after('type');

            $table->integer('count_rejected_payments')->default(0)->after('person_number');
            
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::table('suscription_orders', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('person_number');
        });
    }
};
