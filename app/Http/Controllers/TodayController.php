<?php

namespace App\Http\Controllers;

use App\Models\PackConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TodayController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeekIso; // 1 = Monday ... 7 = Sunday

        $children = $request->user()->children()->with([
            'timetableEntries' => function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)->orderBy('period')->with('subject');
            },
        ])->get();

        $confirmedSubjectIds = PackConfirmation::whereIn('child_id', $children->pluck('id'))
            ->where('date', $today->toDateString())
            ->get()
            ->groupBy('child_id')
            ->map(fn ($rows) => $rows->pluck('subject_id')->all());

        return [
            'day_of_week' => $dayOfWeek,
            'children' => $children->map(function ($child) use ($confirmedSubjectIds) {
                $confirmed = $confirmedSubjectIds->get($child->id, []);

                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'subjects' => $child->timetableEntries->pluck('subject')->unique('id')->values()->map(fn ($subject) => [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'color' => $subject->color,
                        'confirmed' => in_array($subject->id, $confirmed, true),
                    ]),
                ];
            }),
        ];
    }
}
