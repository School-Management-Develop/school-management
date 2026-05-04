<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    private function getTeacherGroup(): Group
    {
        return Group::firstOrCreate(['group_name' => 'Teacher']);
    }

    public function index(Request $request)
    {
        $teacherGroup = $this->getTeacherGroup();
        $q = $request->input('q');

        $teachers = Student::where('group_id', $teacherGroup->group_id)
            ->when($q, fn($query) => $query
                ->where('student_name', 'like', "%{$q}%")
                ->orWhere('phone_number', 'like', "%{$q}%")
            )
            ->orderBy('student_name')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => Student::where('group_id', $teacherGroup->group_id)->count(),
            'active'   => Student::where('group_id', $teacherGroup->group_id)->where('status', 1)->count(),
            'inactive' => Student::where('group_id', $teacherGroup->group_id)->where('status', 0)->count(),
        ];

        return view('backend.page.teachers.index', compact('teachers', 'stats', 'teacherGroup'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'gender'       => ['required', 'in:Male,Female'],
            // 'status'       => ['required', 'in:0,1'],
        ]);

        $teacherGroup = $this->getTeacherGroup();

        Student::create([
            'student_name' => $request->student_name,
            'gender'       => $request->gender,
            'phone_number' => $request->boolean('phone_unknown') ? null : ($request->phone_number ?: null),
            'group_id'     => $teacherGroup->group_id,
            // 'status'       => $request->status,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher added successfully.');
    }

    public function update(Request $request, $id)
    {
        $teacherGroup = $this->getTeacherGroup();
        $teacher      = Student::where('group_id', $teacherGroup->group_id)->findOrFail($id);

        $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'gender'       => ['required', 'in:Male,Female'],
            // 'status'       => ['required', 'in:0,1'],
        ]);

        $teacher->update([
            'student_name' => $request->student_name,
            'gender'       => $request->gender,
            'phone_number' => $request->boolean('phone_unknown') ? null : ($request->phone_number ?: null),
            'group_id'     => $teacherGroup->group_id,
            // 'status'       => $request->status,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        $teacherGroup = $this->getTeacherGroup();
        $teacher      = Student::where('group_id', $teacherGroup->group_id)->findOrFail($id);
        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }
}