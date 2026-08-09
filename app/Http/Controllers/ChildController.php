<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->children;
    }

    public function show(Request $request, Child $child)
    {
        abort_if($child->user_id !== $request->user()->id, 404);

        return $child;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['sort_order'] = $request->user()->children()->count();
        $data['include_saturday'] = false;
        $data['periods_count'] = 6;

        return $request->user()->children()->create($data);
    }

    public function update(Request $request, Child $child)
    {
        abort_if($child->user_id !== $request->user()->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'include_saturday' => ['sometimes', 'boolean'],
            'periods_count' => ['sometimes', 'integer', 'min:1', 'max:12'],
        ]);

        // Shrinking the timetable drops whatever was assigned to the hours
        // that no longer exist, instead of leaving orphaned entries a user
        // can't see or reach from the grid.
        if (isset($data['periods_count']) && $data['periods_count'] < $child->periods_count) {
            $child->timetableEntries()->where('period', '>', $data['periods_count'])->delete();
        }

        $child->update($data);

        return $child;
    }

    public function destroy(Request $request, Child $child)
    {
        abort_if($child->user_id !== $request->user()->id, 404);

        $child->delete();

        return response()->noContent();
    }
}
