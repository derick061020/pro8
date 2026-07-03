<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('districts')->updateOrInsert(
            ['id' => '180107'],
            [
                'description' => 'San Antonio',
                'province_id' => '1801',
            ]
        );
    }

    public function down(): void
    {
        DB::table('districts')->where('id', '180107')->delete();
    }
};