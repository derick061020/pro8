<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hotel_rent_items', function (Blueprint $table) {
            $table->unsignedBigInteger('sale_note_id')->nullable()->after('hotel_rent_order_id');
            $table->unsignedBigInteger('document_id')->nullable()->after('sale_note_id');
            $table->timestamp('invoiced_at')->nullable()->after('document_id');
        });
    }

    public function down()
    {
        Schema::table('hotel_rent_items', function (Blueprint $table) {
            $table->dropColumn(['sale_note_id', 'document_id', 'invoiced_at']);
        });
    }
};
