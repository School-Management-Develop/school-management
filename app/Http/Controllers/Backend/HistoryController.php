<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\ItemHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemHistory::with([
            'user',
            'approvedByUser',
            'returnedByUser',
            'borrow.student',
            'borrow.item',
        ]);

        if ($request->filled('student')) {
            $student = trim($request->student);

            $query->whereHas('borrow.student', function ($q) use ($student) {
                $q->where('student_name', 'like', "%{$student}%");
            });
        }

        if ($request->filled('user')) {
            $user = trim($request->user);

            $query->whereHas('user', function ($q) use ($user) {
                $q->where('name', 'like', "%{$user}%");
            });
        }

        $histories = $query
            ->orderByDesc('action_at')
            ->paginate(20)
            ->appends($request->query());
        return view('backend.page.borrows.history', compact('histories'));
    }
}