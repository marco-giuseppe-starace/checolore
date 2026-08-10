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

        return $children->map(function ($child) {
            $byDay = $child->timetableEntries
                ->groupBy('day_of_week')
                ->map(fn ($entries) => $entries->pluck('subject')->unique('id')->values()->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'color' => $s->color,
                ]));

            return [
                'id' => $child->id,
                'name' => $child->name,
                'include_saturday' => $child->include_saturday,
                'days' => $byDay,
            ];
        });
    }
}
