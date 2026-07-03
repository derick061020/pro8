<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerAdminClientsTable extends Migration
{
    public function up()
    {
        Schema::create('reseller_admin_clients', function (Blueprint $table) {
            $table->unsignedInteger('admin_user_id');
            $table->unsignedInteger('client_id');
            $table->primary(['admin_user_id', 'client_id']);

            $table->foreign('admin_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reseller_admin_clients');
    }
}
