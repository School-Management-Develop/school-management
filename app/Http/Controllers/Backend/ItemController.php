<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

        Item::create([
            'name'        => $request->name,
            'name_kh'     => $request->name_kh,
            'qty'         => $request->qty,
            'status'      => $request->status,
            'description' => $request->description,
            'image'       => $path,
            'available'   => 0,
            'borrow'      => 0,
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

        $item->delete();

        return redirect()->route('items.index')->with('success', __('app.Item deleted!'));
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
}