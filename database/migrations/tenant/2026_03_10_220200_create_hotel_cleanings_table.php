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
        Schema::create('hotel_cleanings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('hotel_room_id'); // Para hotel_rooms (increments)
            $table->unsignedInteger('user_id'); // Para users (increments)
            $table->dateTime('start_time')->nullable(); // Fecha y hora de inicio
            $table->dateTime('end_time')->nullable(); // Fecha y hora de fin
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['hotel_room_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // Agregar claves foráneas después de crear la tabla
        Schema::table('hotel_cleanings', function (Blueprint $table) {
            $table->foreign('hotel_room_id')
                  ->references('id')
                  ->on('hotel_rooms')
                  ->onDelete('cascade');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel_cleanings', function (Blueprint $table) {
            $table->dropForeign(['hotel_room_id']);
            $table->dropForeign(['user_id']);
        });
        
        Schema::dropIfExists('hotel_cleanings');
    }
};
