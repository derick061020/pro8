<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnVisibilityConfigsTable extends Migration
{
    public function up()
    {
        Schema::create('column_visibility_configs', function (Blueprint $table) {
            $table->id();
            $table->string('module')->unique();
            $table->json('columns');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('column_visibility_configs');
    }
}
