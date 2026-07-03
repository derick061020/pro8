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
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('amount', 12, 2)->default(0);

            // Límites de compra
            $table->boolean('has_purchase_limits')->default(false);
            $table->decimal('min_amount', 12, 2)->nullable();
            $table->decimal('max_amount', 12, 2)->nullable();

            // Límites de uso y vigencia
            $table->boolean('has_usage_limits')->default(false);
            $table->unsignedInteger('max_total_uses')->nullable();
            $table->unsignedInteger('max_uses_per_customer')->nullable();
            $table->date('expires_at')->nullable();

            // Envío gratis
            $table->boolean('free_shipping')->default(false);

            // Estado
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_coupons');
    }
};
