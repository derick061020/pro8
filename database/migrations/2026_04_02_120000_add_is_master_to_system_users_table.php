<?php

use App\Models\System\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsMasterToSystemUsersTable extends Migration
{
    public function up()
    {
        $connection = (new User())->getConnectionName();
        $table = User::systemUsersTable();

        Schema::connection($connection)->table($table, function (Blueprint $blueprint) use ($connection, $table) {
            if (! Schema::connection($connection)->hasColumn($table, 'is_master')) {
                $blueprint->boolean('is_master')->default(false)->after('can_create_clients');
            }
        });
    }

    public function down()
    {
        $connection = (new User())->getConnectionName();
        $table = User::systemUsersTable();

        Schema::connection($connection)->table($table, function (Blueprint $blueprint) use ($connection, $table) {
            if (Schema::connection($connection)->hasColumn($table, 'is_master')) {
                $blueprint->dropColumn('is_master');
            }
        });
    }
}
