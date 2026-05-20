<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\ItemActionLog;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $itemsQuery = Item::query();

        if ($q = $request->q) {
            $itemsQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('name_kh', 'like', "%{$q}%");
            });
        }

        $items = $itemsQuery
            ->withSum(['borrows as borrowed_qty' => function ($q) {
                $q->whereIn('status', ['BORROWED', 'OVERDUE']);
            }], 'qty')
            ->orderByDesc('Itemid')
            ->paginate(10)
            ->withQueryString();

        $statTotal = Item::count();
        $statActive = Item::where('status', 1)->count();
        $statInactive = Item::where('status', 0)->count();

        return view('backend.page.items.index', compact('items', 'statTotal', 'statActive', 'statInactive'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'name_kh'     => 'nullable|string|max:255',
            'qty'         => 'required|integer|min:0',
            'status'      => 'required|in:0,1',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->storePublicly('items');
        }

        $item = Item::create([
            'name'        => $request->name,
            'name_kh'     => $request->name_kh,
            'qty'         => $request->qty,
            'status'      => $request->status,
            'description' => $request->description,
            'image'       => $path,
            'available'   => 0,
            'borrow'      => 0,
        ]);

        ItemActionLog::create([
            'item_id'   => $item->Itemid,
            'item_name' => $item->name,
            'user_id'   => Auth::id(),
            'action'    => 'Created',
            'details'   => Auth::user()->name . ' created item "' . $item->name . '" with qty ' . $item->qty,
            'action_at' => now('Asia/Phnom_Penh'),
        ]);

        return back()->with('success', __('app.Item added successfully!'));
    }

    public function update(Request $request, $itemid)
    {
        $item = Item::where('Itemid', $itemid)->firstOrFail();

        $request->validate([
            'name'        => 'required|string|max:255',
            'name_kh'     => 'nullable|string|max:255',
            'qty'         => 'required|integer|min:0',
            'status'      => 'required|in:0,1',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $item->image;
        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::delete($item->image);
            }
            $path = $request->file('image')->storePublicly('items');
        }

        // Track what changed
        $changes = [];
        if ($item->name !== $request->name) $changes[] = 'name: "' . $item->name . '" → "' . $request->name . '"';
        if ($item->name_kh !== $request->name_kh) $changes[] = 'name_kh changed';
        if ($item->qty != $request->qty) $changes[] = 'qty: ' . $item->qty . ' → ' . $request->qty;
        if ($item->status != $request->status) $changes[] = 'status: ' . ($item->status ? 'Active' : 'Inactive') . ' → ' . ($request->status ? 'Active' : 'Inactive');
        if ($request->hasFile('image')) $changes[] = 'image updated';

        $item->save();

        ItemActionLog::create([
            'item_id'   => $item->Itemid,
            'item_name' => $item->name,
            'user_id'   => Auth::id(),
            'action'    => 'Updated',
            'details'   => Auth::user()->name . ' updated item "' . $item->name . '"'
                        . (count($changes) ? ': ' . implode(', ', $changes) : ''),
            'action_at' => now('Asia/Phnom_Penh'),
        ]);
        $item->image       = $path;
        $item->name        = $request->name;
        $item->name_kh     = $request->name_kh;
        $item->qty         = $request->qty;
        $item->status      = $request->status;
        $item->description = $request->description;
        $item->save();

        return back()->with('success', __('app.Item updated successfully!'));
    }

    public function destroy(Request $request, $itemid)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (! Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => __('app.The password is incorrect password')]);
        }

        $item = Item::where('Itemid', $itemid)->firstOrFail();

        if ($item->image) {
            Storage::delete($item->image);
        }
        if (($item->status ?? 1) == 1) {
                return back()->withErrors(['error' => __('app.Cannot delete an active item. Please set the item to inactive first.')]);
            }
        $item->delete();

        ItemActionLog::create([
            'item_id'   => $item->Itemid,
            'item_name' => $item->name,
            'user_id'   => Auth::id(),
            'action'    => 'Deleted',
            'details'   => Auth::user()->name . ' deleted item "' . $item->name . '"',
            'action_at' => now('Asia/Phnom_Penh'),
        ]);
        return redirect()->route('items.index')->with('success', __('app.Item deleted!'));
    }
    public function trashed()
    {
        $trashedItems = Item::onlyTrashed()->latest('deleted_at')->get();

        if (request()->ajax()) {
            return response()->json([
                'html'  => view('backend.page.items.trashed-rows', compact('trashedItems'))->render(),
                'total' => $trashedItems->count(),
            ]);
        }

        return back();
    }
    public function restore($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);
        $item->restore();

        ItemActionLog::create([
            'item_id'   => $item->Itemid,
            'item_name' => $item->name,
            'user_id'   => Auth::id(),
            'action'    => 'Restored',
            'details'   => Auth::user()->name . ' restored item "' . $item->name . '"',
            'action_at' => now('Asia/Phnom_Penh'),
        ]);

        return back()->with('success', __('app.Item restored successfully.'));
    }

    public function show($itemid)
    {
        $item = Item::where('Itemid', $itemid)->firstOrFail();

        $borrowed = Borrow::where('item_id', $item->Itemid)
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->sum('qty');

        $available = $item->qty ?? 0;

        return view('backend.page.items.show', compact('item', 'available', 'borrowed'));
    }
public function actionLogs(Request $request)
{
    $q = $request->q;

    $logs = ItemActionLog::with('user')
        ->when($q, function ($query) use ($q) {
            $query->where('item_name', 'like', "%$q%")
                  ->orWhereHas('user', function ($u) use ($q) {
                      $u->where('name', 'like', "%$q%");
                  });
        })
        ->latest('action_at')
        ->paginate(20)
        ->appends($request->query());

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'html'      => view('backend.page.items.action_logs_rows', compact('logs'))->render(),
            'total'     => $logs->total(),
            'next_page' => $logs->hasMorePages() ? $logs->currentPage() + 1 : null,
            'prev_page' => $logs->currentPage() > 1 ? $logs->currentPage() - 1 : null,
        ]);
    }

    return view('backend.page.items.action_logs', compact('logs'));
}
}