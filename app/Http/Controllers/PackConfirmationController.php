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

        $today = Carbon::now()->toDateString();

        $existing = PackConfirmation::where([
            'child_id' => $child->id,
            'subject_id' => $subject->id,
            'date' => $today,
        ])->first();

        if ($existing) {
            $existing->delete();

            return ['confirmed' => false];
        }

        PackConfirmation::create([
            'child_id' => $child->id,
            'subject_id' => $subject->id,
            'date' => $today,
        ]);

        return ['confirmed' => true];
    }
}
