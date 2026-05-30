<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_rent_changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('hotel_rent_id');
            $table->string('change_type'); // CHECKIN, CHECKOUT, EXTENSION, DATE_EDIT, ROOM_CHANGE, PRICE_CHANGE
            $table->json('old_values')->nullable(); // Valores antes del cambio
            $table->json('new_values')->nullable(); // Valores después del cambio
            $table->text('notes')->nullable(); // Notas adicionales
            $table->decimal('price_difference', 12, 2)->default(0); // Diferencia de precio si aplica
            $table->unsignedInteger('user_id')->nullable(); // Usuario que realizó el cambio
            $table->timestamps();
            
            $table->foreign('hotel_rent_id')->references('id')->on('hotel_rents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['hotel_rent_id', 'change_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_rent_changes');
    }
};
