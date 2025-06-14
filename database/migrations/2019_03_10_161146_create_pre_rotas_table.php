<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreRotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_rotas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('details')->nullable();
            $table->enum('type',['Authorized holiday','Unauthorized holiday','Regular'])->nullable();
            $table->timestamps();
        });
        Schema::create('employee_pre_rota', function (Blueprint $table) {
            $table->integer('employee_id')->unsigned()->index();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->integer('pre_rota_id')->unsigned()->index();
            $table->foreign('pre_rota_id')->references('id')->on('pre_rotas')->onDelete('cascade');
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
        Schema::dropIfExists('pre_rotas');
        Schema::dropIfExists('employee_pre_rota');
    }
}
