<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->string('password')->nullable();
            $table->timestamp('join_date')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_contact_person')->nullable();
            $table->string('ni')->nullable();
            $table->string('tax_code')->nullable();
            $table->string('nationality')->nullable();
            $table->text('bank_details')->nullable();
            $table->integer('entitled_holiday')->default(0);
//            $table->integer('pension_percentage')->nullable();
            $table->string('address')->nullable();
            $table->enum('employee_type',['casual','part time', 'full time']);
            $table->decimal('pay_rate')->default(0);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('employees');
    }
}
