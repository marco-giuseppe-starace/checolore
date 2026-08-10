<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per subject a child has actually confirmed packing on a
        // given calendar date — the "ritual" behind Zaino di oggi. Keyed by
        // date (not day_of_week) since it must reset every real day, not
        // repeat with the weekly timetable.
        Schema::create('pack_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();

            $table->unique(['child_id', 'subject_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_confirmations');
    }
};
