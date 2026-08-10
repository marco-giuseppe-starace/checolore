<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\PackConfirmation;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PackConfirmationController extends Controller
{
    public function toggle(Request $request, Child $child, Subject $subject)
    {
        abort_if($child->user_id !== $request->user()->id, 404);
        abort_if($subject->child_id !== $child->id, 404);

        // Only "today" or "tomorrow" are ever valid — kids often pack the
        // night before, and confirming against tomorrow's real date means
        // it's already marked done once tomorrow actually arrives. No
        // arbitrary client-supplied date is trusted.
        $when = $request->validate(['when' => ['sometimes', 'in:today,tomorrow']])['when'] ?? 'today';
        $date = $when === 'tomorrow' ? Carbon::now()->addDay()->toDateString() : Carbon::now()->toDateString();

        $existing = PackConfirmation::where([
            'child_id' => $child->id,
            'subject_id' => $subject->id,
            'date' => $date,
        ])->first();

        if ($existing) {
            $existing->delete();

            return ['confirmed' => false];
        }

        PackConfirmation::create([
            'child_id' => $child->id,
            'subject_id' => $subject->id,
            'date' => $date,
        ]);

        return ['confirmed' => true];
    }
}
