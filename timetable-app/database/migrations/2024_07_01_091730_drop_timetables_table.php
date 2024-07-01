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
       
        Schema::dropIfExists('timetables');
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
               // Optionally, recreate the table if needed
               Schema::create('timetables', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
    
    }
};
