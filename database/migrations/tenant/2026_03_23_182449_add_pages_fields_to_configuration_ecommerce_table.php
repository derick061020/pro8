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
        if (!Schema::hasTable('configuration_ecommerce')) {
            return;
        }

        Schema::table('configuration_ecommerce', function (Blueprint $table) {

            $hasPreferences = Schema::hasColumn('configuration_ecommerce', 'preferences');

            if (!Schema::hasColumn('configuration_ecommerce', 'about_us')) {
                $column = $table->longText('about_us')->nullable();

                if ($hasPreferences) {
                    $column->after('preferences');
                }
            }

            if (!Schema::hasColumn('configuration_ecommerce', 'privacy_policy')) {
                $column = $table->longText('privacy_policy')->nullable();

                if ($hasPreferences) {
                    $column->after('preferences');
                }
            }

            if (!Schema::hasColumn('configuration_ecommerce', 'terms_conditions')) {
                $column = $table->longText('terms_conditions')->nullable();

                if ($hasPreferences) {
                    $column->after('preferences');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (!Schema::hasTable('configuration_ecommerce')) {
            return;
        }

        Schema::table('configuration_ecommerce', function (Blueprint $table) {

            $columnsToDrop = [];

            if (Schema::hasColumn('configuration_ecommerce', 'terms_conditions')) {
                $columnsToDrop[] = 'terms_conditions';
            }

            if (Schema::hasColumn('configuration_ecommerce', 'privacy_policy')) {
                $columnsToDrop[] = 'privacy_policy';
            }

            if (Schema::hasColumn('configuration_ecommerce', 'about_us')) {
                $columnsToDrop[] = 'about_us';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
