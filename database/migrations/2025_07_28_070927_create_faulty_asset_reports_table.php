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
        Schema::create('faulty_asset_reports', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->foreignId('asset_type_id')->nullable()->constrained('asset_types')->onDelete('cascade');
            $table->foreignId('stock_asset_type_id')->nullable()->constrained('stock_asset_types')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('cascade');
            $table->foreignId('maintenance_id')->nullable()->constrained('maintenances')->onDelete('cascade');
            $table->unsignedInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->tinyInteger('status')->nullable();
            // 1 == Assigned, 2 == In storage, 3 == Under Repair, 4 == Damaged
            $table->longText('note')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faulty_asset_reports');
    }
};
