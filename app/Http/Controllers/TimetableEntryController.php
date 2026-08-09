<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;

class TimetableEntryController extends Controller
{
    public function index(Request $request, Child $child)
    {
        abort_if($child->user_id !== $request->user()->id, 404);

        return $child->timetableEntries()->with('subject')->get();
    }

    public function store(Request $request, Child $child)
    {
        abort_if($child->user_id !== $request->user()->id, 404);

        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'period' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        abort_if($child->subjects()->whereKey($data['subject_id'])->doesntExist(), 422, 'Materia non valida per questo figlio.');

        // A slot (day + period) can only hold one subject — assigning a new
        // one to an already-filled slot replaces it rather than erroring,
        // which matches how someone actually edits a timetable.
        $entry = $child->timetableEntries()->updateOrCreate(
            ['day_of_week' => $data['day_of_week'], 'period' => $data['period']],
            ['subject_id' => $data['subject_id']]
        );

        return $entry->load('subject');
    }

    public function destroy(Request $request, TimetableEntry $timetableEntry)
    {
        abort_if($timetableEntry->child->user_id !== $request->user()->id, 404);

        $timetableEntry->delete();

        return response()->noContent();
    }
}
