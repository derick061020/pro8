<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDashboardGoalToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('configurations', function(Blueprint $table) {
            $table->boolean('dashboard_goal_enabled')->default(false)->after('dashboard_products');
            $table->decimal('dashboard_goal_amount', 15, 2)->default(0)->after('dashboard_goal_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('configurations', function(Blueprint $table) {
            $table->dropColumn(['dashboard_goal_enabled', 'dashboard_goal_amount']);
        });
    }
}
