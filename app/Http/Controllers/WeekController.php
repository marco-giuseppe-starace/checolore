<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeekController extends Controller
{
    public function index(Request $request)
    {
        $children = $request->user()->children()->with([
            'timetableEntries' => function ($query) {
                $query->orderBy('day_of_week')->orderBy('period')->with('subject');
            },
        ])->get();

        return $children->map(fn ($child) => [
            'id' => $child->id,
            'name' => $child->name,
            'include_saturday' => $child->include_saturday,
            'periods_count' => $child->periods_count,
            'entries' => $child->timetableEntries->map(fn ($entry) => [
                'day_of_week' => $entry->day_of_week,
                'period' => $entry->period,
                'subject' => [
                    'id' => $entry->subject->id,
                    'name' => $entry->subject->name,
                    'color' => $entry->subject->color,
                ],
            ]),
        ]);
    }
}
