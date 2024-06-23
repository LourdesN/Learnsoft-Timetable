<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveScheduleIdFromNewTimetablesTable extends Migration
{
    public function up()
    {
        Schema::table('new_timetables', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['schedule_id']);
            
            // Drop the schedule_id column
            $table->dropColumn('schedule_id');
        });
    }

    public function down()
    {
        Schema::table('new_timetables', function (Blueprint $table) {
            // Add the schedule_id column back
            $table->unsignedBigInteger('schedule_id')->nullable();
            
            // Re-add the foreign key constraint
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }
}

