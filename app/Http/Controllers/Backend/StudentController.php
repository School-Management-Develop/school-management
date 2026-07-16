<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // Get Teacher group ID to exclude from student list
        $teacherGroup   = Group::where('group_name', 'Teacher')->first();
        $teacherGroupId = $teacherGroup?->group_id;

        // Single query — always excludes the Teacher group
        $studentsQuery = Student::with('group')
            ->withCount('borrows')
            ->when($teacherGroupId, fn($q) => $q->where('group_id', '!=', $teacherGroupId));
            
        if ($request->filled('group_id')) {
        $studentsQuery->where('group_id', $request->group_id);
}

        if ($request->filled('status')) {
            $studentsQuery->where('status', $request->status);
        }

        // Borrow count sort
        if ($request->filled('borrow_sort')) {
            $studentsQuery->orderBy('borrows_count', $request->borrow_sort);
        } else {
            $studentsQuery->orderByDesc('student_id');
        }
        // Multi-column search
        if ($request->filled('q')) {
            $q = trim($request->q);

            $studentsQuery->where(function ($query) use ($q) {
                $query->where('student_name', 'like', "%{$q}%")
                    ->orWhere('phone_number', 'like', "%{$q}%")
                    ->orWhereHas('group', function ($groupQuery) use ($q) {
                        $groupQuery->where('group_name', 'like', "%{$q}%");
                    });

                if (strtolower($q) === 'active') {
                    $query->orWhere('status', 1);
                }

                if (strtolower($q) === 'inactive') {
                    $query->orWhere('status', 0);
                }
            });
        }

        $students = $studentsQuery->orderByDesc('student_id')
            ->paginate(20)
            ->withQueryString();

        // Stats — all exclude the Teacher group
        $baseStats    = Student::when($teacherGroupId, fn($q) => $q->where('group_id', '!=', $teacherGroupId));
        $statTotal    = (clone $baseStats)->count();
        $statActive   = (clone $baseStats)->where('status', 1)->count();
        $statInactive = (clone $baseStats)->where('status', 0)->count();

        // Groups dropdown — exclude Teacher
        $groups = Group::where('group_name', '!=', 'Teacher')
            ->orderBy('group_name')
            ->get();

        return view('backend.page.students.index', compact(
            'students',
            'groups',
            'statTotal',
            'statActive',
            'statInactive'
        ));
    }

    public function store(Request $request)
    {
        $messages = [
            'phone_number.unique'        => 'This phone number already exists.',
            'inactive_note.required_if' => 'Please provide a note explaining why this student is inactive.',
        ];

        $data = $request->validate([
            'student_name'  => ['required', 'string', 'max:255'],
            'gender'        => ['required', 'in:Male,Female'],
            'phone_number'  => ['nullable', 'string', 'max:50', 'unique:students,phone_number'],
            'group_id'      => ['required', 'exists:groups,group_id'],
            'status'        => ['required', 'in:0,1'],
            'inactive_note' => ['required_if:status,0', 'nullable', 'string', 'max:1000'],
        ], $messages);

        if ((int) $data['status'] === 1) {
            $data['inactive_note'] = null;
        }

        // Rule::unique cannot be used here because it passes the raw input
        // to SQL. We normalize first, then check manually.
        $normalized = strtolower(
            preg_replace('/[\s\x{200B}\x{200C}\x{FEFF}\x{00A0}]+/u', '', $request->student_name)
        );

        $duplicate = Student::where('student_name_normalized', $normalized)
            ->where('group_id', $request->group_id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withErrors(['student_name' => 'A student with this name (or an equivalent spelling) already exists in this group.'])
                ->withInput();
        }

        Student::create($data);

        return redirect()->route('students.index')->with('success', __('app.Student added.'));
    }

    public function show($studentid)
    {
        $student = Student::with('group')->where('student_id', $studentid)->firstOrFail();

        return view('backend.page.students.show', compact('student'));
    }

    public function update(Request $request, $student_id)
    {
        $student = Student::where('student_id', $student_id)->firstOrFail();

        $messages = [
            'phone_number.unique'        => 'This phone number already exists.',
            'inactive_note.required_if' => 'Please provide a note explaining why this student is inactive.',
        ];

        $data = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'gender'       => ['required', 'in:Male,Female'],
            'phone_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'phone_number')->ignore($student->student_id, 'student_id'),
            ],
            'group_id'      => ['required', 'exists:groups,group_id'],
            'status'        => ['required', 'in:0,1'],
            'inactive_note' => ['required_if:status,0', 'nullable', 'string', 'max:1000'],
        ], $messages);

        if ((int) $data['status'] === 1) {
            $data['inactive_note'] = null;
        }

        $normalized = strtolower(
            preg_replace('/[\s\x{200B}\x{200C}\x{FEFF}\x{00A0}]+/u', '', $request->student_name)
        );

        $duplicate = Student::where('student_name_normalized', $normalized)
            ->where('group_id', $request->group_id)
            ->where('student_id', '!=', $student->student_id) // exclude current student
            ->exists();

        if ($duplicate) {
            return back()
                ->withErrors(['student_name' => 'A student with this name (or an equivalent spelling) already exists in this group.'])
                ->withInput();
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', __('app.Student updated.'));
    }

    public function destroy($student_id)
    {
        Student::where('student_id', $student_id)->firstOrFail()->delete();

        return redirect()->route('students.index')->with('success', __('app.Student deleted!'));
    }
}
