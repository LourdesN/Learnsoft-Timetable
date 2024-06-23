<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('new_timetables', function (Blueprint $table) {
            $table->unsignedBigInteger('timeslot_id')->after('teacher_id');
            $table->foreign('timeslot_id')->references('id')->on('timeslots')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('new_timetables', function (Blueprint $table) {
            $table->dropForeign(['timeslot_id']);
            $table->dropColumn('timeslot_id');
        });
    }
};
