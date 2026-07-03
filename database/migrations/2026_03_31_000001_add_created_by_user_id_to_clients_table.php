<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByUserIdToClientsTable extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'created_by_user_id')) {
                $table->unsignedInteger('created_by_user_id')->nullable()->after('id');
                $table->index('created_by_user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'created_by_user_id')) {
                $table->dropIndex(['created_by_user_id']);
                $table->dropColumn('created_by_user_id');
            }
        });
    }
}

