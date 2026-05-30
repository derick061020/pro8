<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to modify the enum column
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('admin', 'seller', 'limpiador') DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Use raw SQL to revert the enum column
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('admin', 'seller') DEFAULT 'admin'");
    }
};
