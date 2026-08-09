<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            // ISO-8601: 1 = Monday ... 7 = Sunday.
            $table->unsignedTinyInteger('day_of_week');
            // Ordering of the hour slot within the day (1st period, 2nd, ...).
            $table->unsignedTinyInteger('period');
            $table->timestamps();

            $table->unique(['child_id', 'day_of_week', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
