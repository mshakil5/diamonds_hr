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
        Schema::create('stock_asset_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
            $table->unsignedBigInteger('asset_type_id')->nullable();
            $table->foreign('asset_type_id')->references('id')->on('asset_types')->onDelete('cascade');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->string('product_code')->nullable();
            $table->string('quantity')->default(1);
            $table->tinyInteger('asset_status')->nullable();
            // 1 == Assigned, 2 == In storage, 3 == Under Repair, 4 == Damaged
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_asset_types');
    }
};
