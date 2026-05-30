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
    public function up(): void
    {
        DB::table('configurations')->get()->each(function ($config) {
            $visual = json_decode($config->visual, true);

            if (!is_array($visual)) {
                $visual = [];
            }

            if (!array_key_exists('branch_selector_in_sidebar', $visual)) {
                $visual['branch_selector_in_sidebar'] = false;
            }

            DB::table('configurations')
                ->where('id', $config->id)
                ->update([
                    'visual' => json_encode($visual),
                ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('configurations')->get()->each(function ($config) {
            $visual = json_decode($config->visual, true);
    
            if (is_array($visual) && array_key_exists('branch_selector_in_sidebar', $visual)) {
                unset($visual['branch_selector_in_sidebar']);
            }
    
            DB::table('configurations')
                ->where('id', $config->id)
                ->update([
                    'visual' => json_encode($visual),
                ]);
        });
    }
};
