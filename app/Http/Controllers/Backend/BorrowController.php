<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Group;
use App\Models\Item;
use App\Models\ItemHistory;
use App\Models\Student;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        Borrow::query()
            ->where('status', 'BORROWED')
            ->whereNull('return_date')
            ->where('borrow_date', '<', now()->subDays(3))
            ->update(['status' => 'OVERDUE']);

        $students = Student::orderBy('student_name')->get();
        $items = Item::orderBy('name')->get();
        $groups = Group::orderBy('group_name')->get();

        $query = Borrow::with([
            'student.group',
            'item',
            'approvedByUser',
            'returnedByUser',
        ]);

        $filter = $request->filter;
        $search = $request->search;

        if ($filter == 'student_name' && $request->filled('search')) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%");
            });
        }

        if ($filter == 'item_name' && $request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($filter == 'group_name' && $request->filled('search')) {
            $query->whereHas('student.group', function ($q) use ($search) {
                $q->where('group_name', 'like', "%{$search}%");
            });
        }

        if ($filter == 'status' && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activeBorrows = Borrow::with([
            'student',
            'item',
            'approvedByUser',
            'returnedByUser',
        ])
            ->whereIn('status', ['BORROWED', 'OVERDUE', 'RETURNED'])
            ->orderByDesc('borrow_date')
            ->get();

        $borrows = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $stats = [
            'total_records' => Borrow::count(),
            'active_records' => Borrow::whereIn('status', ['BORROWED', 'OVERDUE'])->count(),
            'overdue_records' => Borrow::where('status', 'OVERDUE')->count(),
            'returned_records' => Borrow::where('status', 'RETURNED')->count(),
            'total_qty' => Borrow::sum('qty'),
            'borrowed_qty' => Borrow::whereIn('status', ['BORROWED', 'OVERDUE'])->sum('qty'),
            'returned_qty' => Borrow::where('status', 'RETURNED')->sum('qty'),
        ];

        return view('backend.page.borrows.index', compact(
            'students',
            'items',
            'groups',
            'activeBorrows',
            'borrows',
            'stats'
        ));
    }

    public function storeBorrow(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,student_id'],
            'item_id' => ['required'],
            'qty' => ['required', 'integer', 'min:1'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['item_id'] === 'other') {
            return back()
                ->withErrors(['item_id' => 'Please choose an item from the list.'])
                ->withInput();
        }

        $item = Item::where('Itemid', $data['item_id'])->firstOrFail();

        $isSocket = stripos($item->name ?? '', 'socket') !== false;

        if ($isSocket) {
            if (! in_array((int) $data['qty'], [1, 2, 3], true)) {
                return back()
                    ->withErrors(['qty' => 'Socket can only use qty 1, 2, or 3.'])
                    ->withInput();
            }
        } else {
            if ((int) $data['qty'] !== 1) {
                return back()
                    ->withErrors(['qty' => 'This item can only use qty 1.'])
                    ->withInput();
            }
        }

        $activeBorrowed = Borrow::where('item_id', $item->Itemid)
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->sum('qty');
        $available = ($item->qty ?? 0) - $activeBorrowed;

        if ($available < $data['qty']) {
            return back()
                ->withErrors(['qty' => "Not enough stock. Available: {$available}"])
                ->withInput();
        }

        $borrow = Borrow::create([
            'student_id' => $data['student_id'],
            'item_id' => $data['item_id'],
            'qty' => $data['qty'],
            'borrow_date' => now('Asia/Jakarta'),
            'due_date' => $data['due_date'] ?? null,
            'status' => 'BORROWED',
            'notes' => $data['notes'] ?? null,
            'approved_by' => Auth::id(),
        ]);

        $student = Student::where('student_id', $data['student_id'])->first();

        ItemHistory::create([
            'item_id' => $item->Itemid,
            'borrow_id' => $borrow->id,
            'student_id' => $student?->student_id,
            'action' => 'Borrowed',
            'details' => (Auth::user()?->name ?? 'System').' borrowed '.$borrow->qty.' x '.($item->name ?? '-').' for '.($student->student_name ?? '-').'.',
            'user_id' => Auth::id(),
            'approved_by' => Auth::id(),
            'action_at' => now('Asia/Jakarta'),
        ]);

        return redirect()->back()->with('success', 'Borrow created successfully.');
    }

    public function storeReturn(Request $request)
    {
        $data = $request->validate([
            'borrow_id' => [
                'required',
                'exists:borrows,id',
                Rule::exists('borrows', 'id')->where(function ($q) {
                    $q->whereIn('status', ['BORROWED', 'OVERDUE']);
                }),
            ],
            'return_date' => ['required', 'date_format:Y-m-d\TH:i'],
            'condition' => ['required', 'string', 'max:50'],
            'return_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $borrow = Borrow::with(['student', 'item', 'approvedByUser'])
            ->where('id', $data['borrow_id'])
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->firstOrFail();

        $returnDate = Carbon::createFromFormat('Y-m-d\TH:i', $data['return_date'], 'Asia/Jakarta');

        $borrow->update([
            'return_date' => $returnDate,
            'condition' => $data['condition'],
            'return_notes' => $data['return_notes'] ?? null,
            'status' => 'RETURNED',
            'returned_by' => Auth::id(),
        ]);

        ItemHistory::create([
            'borrow_id' => $borrow->id,
            'student_id' => $borrow->student_id,
            'item_id' => $borrow->item_id,
            'user_id' => Auth::id(),
            'approved_by' => $borrow->approved_by,
            'returned_by' => $borrow->returned_by,
            'action' => 'Returned',
            'details' => (Auth::user()?->name ?? 'System').' returned '.$borrow->qty.' x '.($borrow->item->display_name ?? '-').' from '.($borrow->student->student_name ?? '-').'.',
            'action_at' => $returnDate,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Return saved successfully.',
                'borrow' => $this->borrowData($borrow),
            ]);
        }

        return redirect()->route('borrows.index')->with('success', __('app.Return saved.'));
    }

    public function undoReturn(Request $request, Borrow $borrow)
    {
        if ($borrow->status !== 'RETURNED') {
            return back()->withErrors(['status' => 'Only RETURNED records can be undone.']);
        }

        $borrow->load(['student', 'item']);

        // Check availability dynamically before undoing
        $activeBorrowed = Borrow::where('item_id', $borrow->item_id)
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->sum('qty');
        $available = ($borrow->item->qty ?? 0) - $activeBorrowed;

        if ($available < ($borrow->qty ?? 1)) {
            return back()->withErrors([
                'qty' => "Cannot undo return. Not enough stock available ({$available}).",
            ]);
        }

        $oldReturnedBy = $borrow->returned_by;

        $borrow->update([
            'status' => 'BORROWED',
            'return_date' => null,
            'condition' => null,
            'return_notes' => null,
            'returned_by' => null,
        ]);

        ItemHistory::create([
            'borrow_id' => $borrow->id,
            'student_id' => $borrow->student_id,
            'item_id' => $borrow->item_id,
            'user_id' => Auth::id(),
            'approved_by' => $borrow->approved_by,
            'returned_by' => $oldReturnedBy,
            'action' => 'Undo return',
            'details' => (Auth::user()?->name ?? 'System').' undid the return for '.$borrow->qty.' x '.($borrow->item->display_name ?? '-').' from '.($borrow->student->student_name ?? '-').'.',
            'action_at' => now('Asia/Jakarta'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Undo return successful.',
                'borrow' => $this->borrowData($borrow, returnDate: '-', condition: '-', returnNotes: '-', returnedBy: '-'),
            ]);
        }

        return redirect()->route('borrows.index')->with('success', 'Return undone. Status is BORROWED again.');
    }

    public function destroy(Borrow $borrow)
    {
        $borrow->deleted_by = Auth::id();
        $borrow->save();
        $borrow->delete(); // soft delete — sets deleted_at

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Borrow record moved to trash.',
                'id' => $borrow->id,
            ]);
        }

        return redirect()->route('borrows.index')
            ->with('success', 'Borrow record moved to trash.');
    }

    public function trashed(Request $request)
    {
        $q = $request->input('q');

        $trashed = Borrow::onlyTrashed()
            ->with(['student.group', 'item', 'deletedByUser'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('student', fn ($s) => $s->where('student_name', 'like', "%{$q}%"))
                        ->orWhereHas('item', fn ($i) => $i->where('name', 'like', "%{$q}%"));
                });
            })
            ->latest('deleted_at')
            ->paginate(15);

        return response()->json([
            'html' => view('backend.page.borrows.trashed-rows', compact('trashed'))->render(),
            'total' => $trashed->total(),
        ]);
    }

    public function restore(int $id)
    {
        $borrow = Borrow::onlyTrashed()->findOrFail($id);

        $borrow->deleted_by = null;
        $borrow->restore(); // clears deleted_at

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Borrow record restored successfully.',
                'id' => $borrow->id,
            ]);
        }

        return redirect()->route('borrows.index')
            ->with('success', 'Borrow record restored successfully.');
    }

    public function update(Request $request)
    {
        $borrow = Borrow::findOrFail($request->borrow_id);

        if ($borrow->status === 'RETURNED') {
            return back()->withErrors(['error' => 'Cannot edit a returned borrow record.']);
        }

        $newItem = Item::findOrFail($request->item_id);
        $newQty = (int) $request->qty;

        // Calculate availability dynamically, excluding the current borrow
        // so we don't count it against itself
        $activeBorrowed = Borrow::where('item_id', $newItem->Itemid)
            ->where('id', '!=', $borrow->id)
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->sum('qty');

        $available = ($newItem->qty ?? 0) - $activeBorrowed;

        if ($available < $newQty) {
            return back()->withErrors([
                'error' => "Not enough stock. Available: {$available}",
            ]);
        }

        $borrow->update([
            'student_id' => $request->student_id,
            'item_id' => $request->item_id,
            'qty' => $newQty,
            'notes' => $request->notes,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Borrow updated successfully.',
                'borrow' => $this->borrowData($borrow),
            ]);
        }

        return back()->with('success', 'Borrow updated successfully.');
    }

    public function updateCallStatus(Request $request, Borrow $borrow)
{
    $request->validate([
        'call_status' => 'required|in:not_yet_called,called_done,no_answer,wrong_number',
        'call_note'   => 'nullable|string|max:1000',
    ]);

    if ($request->call_status === 'wrong_number' && blank($request->call_note)) {
        return back()->withErrors([
            'call_note' => 'Please write note when phone number is wrong.',
        ]);
    }

    $borrow->load(['student', 'item']);

    $statusLabels = [
        'not_yet_called' => 'Not Yet Called',
        'called_done'    => 'Called Done',
        'no_answer'      => 'No Answer',
        'wrong_number'   => 'Wrong Number',
    ];

    $borrow->update([
        'call_status' => $request->call_status,
        'call_note'   => $request->call_note,
        'called_at'   => $request->call_status === 'not_yet_called' ? null : now(),
        'called_by'   => $request->call_status === 'not_yet_called' ? null : Auth::id(),
    ]);

    if ($request->call_status !== 'not_yet_called') {
        ItemHistory::create([
            'borrow_id'   => $borrow->id,
            'student_id'  => $borrow->student_id,
            'item_id'     => $borrow->item_id,
            'user_id'     => Auth::id(),
            'approved_by' => $borrow->approved_by,
            'returned_by' => null,
            'action'      => 'Called',
            'details'     => (Auth::user()?->name ?? 'System')
                . ' called ' . ($borrow->student->student_name ?? '-')
                . ' — ' . ($statusLabels[$request->call_status] ?? $request->call_status)
                . ($request->call_note ? ' | Note: ' . $request->call_note : ''),
            'action_at'   => now('Asia/Phnom_Penh'),
        ]);
    }

    return back()->with('success', 'Call status updated successfully.');
}

    public function lateReturns(Request $request)
    {
        $q = $request->q;

        $lateReturns = Borrow::with(['student', 'item'])
            ->whereNotNull('return_date')
            ->whereRaw('TIMESTAMPDIFF(HOUR, borrow_date, return_date) > 72')
            ->when($q, function ($query) use ($q) {
                $query->whereHas('student', function ($s) use ($q) {
                    $s->where('student_name', 'like', "%$q%");
                })->orWhereHas('item', function ($i) use ($q) {
                    $i->where('name', 'like', "%$q%");
                });
            })
            ->orderByDesc('return_date')
            ->paginate(10);

        return view('backend.page.borrows.late_returns', compact('lateReturns'));
    }

    public function overdueBorrows(Request $request)
    {
        $q = $request->get('q');

        $query = Borrow::query()
            ->whereNull('return_date')
            ->where('borrow_date', '<', now()->subDays(2))
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->with(['student', 'item', 'calledByUser']);

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->whereHas('student', function ($s) use ($q) {
                    $s->where('student_name', 'like', "%{$q}%")
                        ->orWhere('phone_number', 'like', "%{$q}%");
                })->orWhereHas('item', function ($i) use ($q) {
                    $i->where('name', 'like', "%{$q}%");
                });
            });
        }

        $overdues = $query
            ->orderBy('borrow_date', 'asc')
            ->paginate(10)
            ->appends($request->query());

        return view('backend.page.borrows.overdue', compact('overdues'));
    }

    public function itemHistory(Request $request)
    {
        $query = ItemHistory::with([
            'user',
            'approvedByUser',
            'returnedByUser',
            'borrow.student',
            'borrow.item',
        ]);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('student')) {
            $query->whereHas('borrow.student', function ($q) use ($request) {
                $q->where('student_name', 'like', '%'.$request->student.'%');
            });
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $histories = $query
            ->latest('action_at')
            ->paginate(20)
            ->appends($request->query());

        $users = User::orderBy('name')->get();
        $items = Item::orderBy('name')->get();

        return view('backend.page.borrows.history', compact('histories', 'users', 'items'));

    }

    // ── Shared helper to build borrow JSON response ──────────────
    private function borrowData(Borrow $borrow, ?string $returnDate = null, ?string $condition = null, ?string $returnNotes = null, ?string $returnedBy = null): array
    {
        return [
            'id' => $borrow->id,
            'student_name' => $borrow->student->student_name ?? 'N/A',
            'gender' => $borrow->student->gender ?? 'N/A',
            'group_name' => $borrow->student->group->group_name ?? 'N/A',
            'item_name' => $borrow->item->display_name ?? 'N/A',
            'qty' => $borrow->qty,
            'borrow_date' => $borrow->borrow_date ? Carbon::parse($borrow->borrow_date)->format('d M Y H:i') : '-',
            'return_date' => $returnDate ?? ($borrow->return_date ? Carbon::parse($borrow->return_date)->format('d M Y H:i') : '-'),
            'status' => $borrow->status,
            'condition' => $condition ?? ($borrow->condition ?? '-'),
            'notes' => $borrow->notes ?? '-',
            'return_notes' => $returnNotes ?? ($borrow->return_notes ?? '-'),
            'approved_by' => $borrow->approvedByUser->name ?? '-',
            'returned_by' => $returnedBy ?? ($borrow->returnedByUser->name ?? '-'),
            'student_id' => $borrow->student_id,
            'item_id' => $borrow->item_id,
        ];
    }
}
