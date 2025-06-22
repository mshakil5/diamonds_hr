<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeClockinClockoutTypeInAttendancesTable extends Migration
{
    public function up()
    {
        // Step 1: Add temporary string columns
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('clock_in_temp')->nullable();
            $table->string('clock_out_temp')->nullable();
        });

        // Step 2: Convert the values to 'YYYY-MM-DD HH:MM' using MySQL string functions
        DB::statement("
            UPDATE attendances 
            SET 
                clock_in_temp = DATE_FORMAT(STR_TO_DATE(clock_in, '%m/%d/%Y %H:%i'), '%Y-%m-%d %H:%i'),
                clock_out_temp = DATE_FORMAT(STR_TO_DATE(clock_out, '%m/%d/%Y %H:%i'), '%Y-%m-%d %H:%i')
            WHERE clock_in IS NOT NULL OR clock_out IS NOT NULL
        ");

        // Step 3: Drop the old columns
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('clock_in');
            $table->dropColumn('clock_out');
        });

        // Step 4: Rename the temp columns back
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('clock_in')->nullable();
            $table->string('clock_out')->nullable();
        });

        DB::statement("
            UPDATE attendances 
            SET 
                clock_in = clock_in_temp,
                clock_out = clock_out_temp
        ");

        // Step 5: Drop the temp columns
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('clock_in_temp');
            $table->dropColumn('clock_out_temp');
        });
    }

    public function down()
    {
        // Optional rollback
    }
}

