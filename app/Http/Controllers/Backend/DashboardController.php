<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Group;
use App\Models\Item;
use App\Models\Student;
use App\Models\StudentSubmission;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents    = Student::count();
        $activeStudents   = Student::where('status', 1)->count();
        $inactiveStudents = Student::where('status', 0)->count();

        $totalGroups = Group::count();

        $totalItems = Item::count();

        // ✅ FIXED: query by name instead of hardcoded Itemid
        $socketItem = Item::where('name', 'like', '%socket%')
                          ->orWhere('name_kh', 'like', '%socket%')
                          ->first();

        $totalOut        = 0;
        $socketAvailable = 0;

        if ($socketItem) {
            // $totalOut = Borrow::where('item_id', $socketItem->Itemid)
            //     ->whereIn('status', ['BORROWED', 'OVERDUE'])
            //     ->sum('qty');

            // $socketAvailable = ($socketItem->qty ?? 0) - $totalOut;
            $socketAvailable = $socketItem?->available ?? 0;
            $totalOut = $socketItem?->borrow ?? 0;
        }

        $imageSocket = $socketItem; // null safe — blade uses ?->image

        $availableItems = Item::sum('qty');
        $borrowedItems  = Borrow::whereIn('status', ['BORROWED', 'OVERDUE'])->sum('qty');

        $pendingSubmissions = StudentSubmission::where('is_borrow_approved', false)->count();

        $overdueCount = Borrow::whereNull('return_date')
            ->where('borrow_date', '<', now()->subDays(2))
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->count();

        $recentSubmissions = StudentSubmission::with(['group', 'item'])
            ->latest()
            ->take(5)
            ->get();

        $recentBorrows = Borrow::with(['student', 'item'])
            ->latest('borrow_date')
            ->take(5)
            ->get();

        return view('backend.dashboard.index', compact(
            'totalStudents',
            'activeStudents',
            'inactiveStudents',
            'totalGroups',
            'totalItems',
            'socketItem',
            'socketAvailable',
            'availableItems',
            'borrowedItems',
            'totalOut',
            'pendingSubmissions',
            'overdueCount',
            'recentSubmissions',
            'recentBorrows',
            'imageSocket'
        ));
    }
}