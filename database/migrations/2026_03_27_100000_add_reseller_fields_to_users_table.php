<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResellerFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'reseller_id')) {
                $table->unsignedInteger('reseller_id')->nullable()->after('id');
                $table->foreign('reseller_id')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true)->after('api_token');
            }
        });

        // Aseguramos que el usuario principal (Master) siempre tenga status activo (1)
        \Illuminate\Support\Facades\DB::table('users')
            ->whereNull('reseller_id')
            ->orWhere('id', 1)
            ->update(['status' => 1]);
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'reseller_id')) {
                $table->dropForeign(['reseller_id']);
                $table->dropColumn('reseller_id');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
}
