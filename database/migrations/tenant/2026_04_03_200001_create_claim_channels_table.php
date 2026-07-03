<?php

// Modules/ClaimsBook — migración: tabla de canales de recepción de reclamos

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimChannelsTable extends Migration
{
    /**
     * Crea la tabla claim_channels y la pre-pobla con los establecimientos
     * existentes del tenant para que el formulario público tenga canales disponibles.
     */
    public function up()
    {
        Schema::create('claim_channels', function (Blueprint $table) {
            $table->increments('id');

            // Nombre del canal (ej: "Presencial", "Web", "Teléfono")
            $table->string('name', 100);

            $table->timestamps();
        });

        // Sembrar canales desde los establecimientos ya registrados en el tenant.
        $establishments = DB::table('establishments')->get(['description']);

        if ($establishments->isNotEmpty()) {
            DB::table('claim_channels')->insert(
                $establishments->map(fn ($e) => [
                    'name'       => trim($e->description),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray()
            );
        }
    }

    /**
     * Revierte la creación de la tabla claim_channels.
     */
    public function down()
    {
        Schema::dropIfExists('claim_channels');
    }
}
