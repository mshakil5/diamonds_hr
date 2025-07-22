<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_asset_types', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained();
            $table->foreignId('maintenance_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_asset_types', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['maintenance_id']);
            $table->dropColumn(['branch_id', 'maintenance_id']);
        });
    }
};
