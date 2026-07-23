<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UnlockPrimaryAdminUsers extends Migration
{
    /**
     * El usuario Administrador principal (id=1) se creaba con locked=true desde
     * el aprovisionamiento del tenant (ClientController@store), de antes de que
     * existiera el bot de WhatsApp. Al reutilizar `locked` como "suspendido" para
     * el bot, todo tenant nace con el bot del admin principal rechazado. Se
     * corrige el default en el aprovisionamiento y aqui se repara el dato de los
     * tenants ya creados.
     */
    public function up()
    {
        DB::table('users')
            ->where('id', 1)
            ->where('locked', true)
            ->update(['locked' => false]);
    }

    public function down()
    {
        // Corrección de datos, no reversible.
    }
}
