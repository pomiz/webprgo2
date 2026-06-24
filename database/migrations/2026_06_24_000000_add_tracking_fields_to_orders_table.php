<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier')->nullable()->after('shipping_address');
            $table->string('tracking_number')->nullable()->after('courier');
            $table->timestamp('shipped_at')->nullable()->after('tracking_number');
            $table->string('tracking_status')->nullable()->after('shipped_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier', 'tracking_number', 'shipped_at', 'tracking_status']);
        });
    }
};
