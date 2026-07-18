<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ItemHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffRankingController extends Controller
{
    private const CATEGORIES = [
        'borrow' => 'Borrowed',
        'return' => 'Returned',
        'call' => 'Called',
    ];

    public function index(Request $request)
    {
        $start = $request->filled('start_date') ? $request->date('start_date')->startOfDay() : null;
        $end = $request->filled('end_date') ? $request->date('end_date')->endOfDay() : null;

        $rankings = [];
        foreach (self::CATEGORIES as $key => $action) {
            $rankings[$key] = $this->rankByAction($action, $start, $end);
        }

        $overall = $this->rankOverall($start, $end);

        return view('backend.page.staff-ranking.index', [
            'rankings' => $rankings,
            'overall' => $overall,
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
        ]);
    }

    private function rankByAction(string $action, $start, $end)
    {
        return ItemHistory::query()
            ->select('user_id', DB::raw('count(*) as total'))
            ->where('action', $action)
            ->whereNotNull('user_id')
            ->when($start, fn ($q) => $q->where('action_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('action_at', '<=', $end))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user')
            ->take(10)
            ->get();
    }

    private function rankOverall($start, $end)
    {
        return ItemHistory::query()
            ->select('user_id', DB::raw('count(*) as total'))
            ->whereIn('action', self::CATEGORIES)
            ->whereNotNull('user_id')
            ->when($start, fn ($q) => $q->where('action_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('action_at', '<=', $end))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user')
            ->take(10)
            ->get();
    }
}
