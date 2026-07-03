<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemSkinsTable extends Migration
{
    public function up()
    {
        Schema::connection('system')->create('system_skins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('filename');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        DB::connection('system')->table('system_skins')->insert([
            ['name' => 'Default', 'filename' => 'default.css', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Light',   'filename' => 'light.css',   'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Black',   'filename' => 'black.css',   'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Modern',  'filename' => 'modern.css',  'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::connection('system')->dropIfExists('system_skins');
    }
}
