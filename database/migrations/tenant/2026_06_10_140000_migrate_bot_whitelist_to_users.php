<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateBotWhitelistToUsers extends Migration
{
    /**
     * Movemos el registro de "quien usa el bot" desde la tabla bot_whitelist
     * a la tabla users:
     *   - users.personal_cell_phone (el numero ya existia, se usa como identificador del bot)
     *   - users.bot_enabled (nueva columna, toggle por usuario)
     *
     * Si el usuario esta locked=true (suspendido), el bot lo rechaza en codigo
     * aunque bot_enabled=true. La regla locked anula bot_enabled siempre.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'bot_enabled')) {
                $table->boolean('bot_enabled')->default(false)->after('locked');
            }
        });

        if (Schema::hasTable('bot_whitelist')) {
            $rows = DB::connection('tenant')->table('bot_whitelist')
                ->whereNotNull('user_id')
                ->whereNotNull('phone_number')
                ->get();

            foreach ($rows as $row) {
                // Si el user ya tiene personal_cell_phone, no lo pisamos (puede
                // ser un numero distinto que el dueno quiere conservar).
                DB::connection('tenant')->table('users')
                    ->where('id', $row->user_id)
                    ->where(function ($q) {
                        $q->whereNull('personal_cell_phone')->orWhere('personal_cell_phone', '');
                    })
                    ->update(['personal_cell_phone' => $row->phone_number]);

                DB::connection('tenant')->table('users')
                    ->where('id', $row->user_id)
                    ->update(['bot_enabled' => (bool) $row->enabled]);
            }

            Schema::drop('bot_whitelist');
        }
    }

    public function down()
    {
        if (!Schema::hasTable('bot_whitelist')) {
            Schema::create('bot_whitelist', function (Blueprint $table) {
                $table->id();
                $table->string('phone_number', 30)->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('seller_name', 255)->nullable();
                $table->boolean('enabled')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasColumn('users', 'bot_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('bot_enabled');
            });
        }
    }
}
