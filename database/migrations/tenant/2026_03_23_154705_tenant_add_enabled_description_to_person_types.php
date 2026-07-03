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
        Schema::table('person_types', function (Blueprint $table) {
            $table->boolean('enabled_description_person_type')->default(false);
            $table->text('description_person_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('person_types', function (Blueprint $table) {
            $table->dropColumn(['enabled_description_person_type', 'description_person_type']);
        });
    }
};
