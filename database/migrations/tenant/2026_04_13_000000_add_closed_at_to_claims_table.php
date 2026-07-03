<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosedAtToClaimsTable extends Migration
{
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('is_closed');
        });
    }

    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('closed_at');
        });
    }
}
