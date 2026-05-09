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
        Schema::create('daily_pre_rota_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_pre_rota_id')->constrained()->onDelete('cascade'); // ← Added
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date')->nullable();
            $table->string('time_range')->nullable();
            $table->boolean('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_pre_rota_details');
    }
};
