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
        Schema::table('learning_areas', function (Blueprint $table) {
            $table->boolean('double_lessons')->default(false)->after('number_of_lessons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_areas', function (Blueprint $table) {
            $table->dropColumn('double_lessons');
        });
    }
};
