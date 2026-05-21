<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Ruang Baju');
            $table->string('store_city')->nullable();
            $table->string('store_province')->nullable();
            $table->decimal('store_latitude', 10, 7)->nullable();
            $table->decimal('store_longitude', 10, 7)->nullable();
            $table->decimal('shipping_rate_per_km', 10, 2)->default(2000);
            $table->decimal('min_shipping_cost', 10, 2)->default(10000);
            $table->decimal('max_shipping_cost', 10, 2)->default(100000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
