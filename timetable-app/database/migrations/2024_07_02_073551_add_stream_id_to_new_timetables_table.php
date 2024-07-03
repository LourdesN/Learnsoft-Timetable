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
        Schema::table('new_timetables', function (Blueprint $table) {
            $table->unsignedBigInteger('stream_id')->nullable()->after('grade_id');
            $table->foreign('stream_id')->references('id')->on('streams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_timetables', function (Blueprint $table) {
            $table->dropForeign(['stream_id']);
            $table->dropColumn('stream_id');
        });
    }
};
