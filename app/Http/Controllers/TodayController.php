<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TodayController extends Controller
{
    public function index(Request $request)
    {
        $dayOfWeek = Carbon::now()->dayOfWeekIso; // 1 = Monday ... 7 = Sunday

        $children = $request->user()->children()->with([
            'timetableEntries' => function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)->orderBy('period')->with('subject');
            },
        ])->get();

        return [
            'day_of_week' => $dayOfWeek,
            'children' => $children->map(fn ($child) => [
                'id' => $child->id,
                'name' => $child->name,
                'subjects' => $child->timetableEntries->pluck('subject'),
            ]),
        ];
    }
}
