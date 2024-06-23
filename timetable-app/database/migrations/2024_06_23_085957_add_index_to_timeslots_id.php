<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToTimeslotsId extends Migration
{
    public function up()
    {
        Schema::table('timeslots', function (Blueprint $table) {
            $table->index('id');
        });
    }

    public function down()
    {
        Schema::table('timeslots', function (Blueprint $table) {
            $table->dropIndex(['id']);
        });
    }
}
