<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToNewTimetables extends Migration
{
    public function up()
    {
        Schema::table('new_timetables', function (Blueprint $table) {
            $table->foreign('grade_id', 'fk_grade_id')->references('id')->on('grades')->onDelete('cascade');
            $table->foreign('learning_area_id', 'fk_learning_area_id')->references('id')->on('learning_areas')->onDelete('cascade');
            $table->foreign('teacher_id', 'fk_teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('timeslot_id', 'fk_timeslot_id')->references('id')->on('timeslots')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('new_timetables', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['learning_area_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['timeslot_id']);
        });
    }
}

