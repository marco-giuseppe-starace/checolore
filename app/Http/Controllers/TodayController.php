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
        $tomorrow = $today->clone()->addDay();

        $children = $request->user()->children()->with('timetableEntries.subject')->get();

        $confirmedByDate = PackConfirmation::whereIn('child_id', $children->pluck('id'))
            ->whereIn('date', [$today->toDateString(), $tomorrow->toDateString()])
            ->get()
            ->groupBy(fn ($row) => $row->date->toDateString().'-'.$row->child_id)
            ->map(fn ($rows) => $rows->pluck('subject_id')->all());

        $buildDay = function ($children, $dayOfWeek, $date) use ($confirmedByDate) {
            return $children->map(function ($child) use ($dayOfWeek, $date, $confirmedByDate) {
                $confirmed = $confirmedByDate->get($date.'-'.$child->id, []);

                $subjects = $child->timetableEntries
                    ->where('day_of_week', $dayOfWeek)
                    ->sortBy('period')
                    ->pluck('subject')
                    ->unique('id')
                    ->values();

                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'subjects' => $subjects->map(fn ($subject) => [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'color' => $subject->color,
                        'confirmed' => in_array($subject->id, $confirmed, true),
                    ]),
                ];
            });
        };

        return [
            'day_of_week' => $today->dayOfWeekIso,
            'children' => $buildDay($children, $today->dayOfWeekIso, $today->toDateString()),
            'tomorrow' => [
                'day_of_week' => $tomorrow->dayOfWeekIso,
                'children' => $buildDay($children, $tomorrow->dayOfWeekIso, $tomorrow->toDateString()),
            ],
        ];
    }
}
