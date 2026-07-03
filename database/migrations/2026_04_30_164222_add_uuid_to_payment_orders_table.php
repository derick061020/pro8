<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        DB::table('payment_orders')->whereNull('uuid')->orderBy('id')->each(function ($order) {
            DB::table('payment_orders')->where('id', $order->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        });
    }

    public function down()
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
