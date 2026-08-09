<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request, Child $child)
    {
        abort_if($child->user_id !== $request->user()->id, 404);

        return $child->subjects;
    }

    public function store(Request $request, Child $child)
    {
        abort_if($child->user_id !== $request->user()->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $data['sort_order'] = $child->subjects()->count();

        return $child->subjects()->create($data);
    }

    public function update(Request $request, Subject $subject)
    {
        abort_if($subject->child->user_id !== $request->user()->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $subject->update($data);

        return $subject;
    }

    public function destroy(Request $request, Subject $subject)
    {
        abort_if($subject->child->user_id !== $request->user()->id, 404);

        $subject->delete();

        return response()->noContent();
    }
}
