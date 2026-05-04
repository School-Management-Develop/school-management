<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;

        $groupsQuery = Group::query()->withCount('students'); // ✅ add this

        if (!empty($q)) {
            $groupsQuery->where('group_name', 'like', "%{$q}%");
        }

        $groups = $groupsQuery
            ->orderBy('group_name')
            ->paginate(20)
            ->appends($request->query());

        return view('backend.page.groups.index', compact('groups'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255|unique:groups,group_name',
        ]);

        Group::create([
            'group_name' => $request->group_name,
        ]);

        return back()->with('success', __('app.Group added successfully!'));
    }

    public function destroy($group_id)
    {
        Group::where('group_id', $group_id)->firstOrFail()->delete();

        return back()->with('success', __('app.Group deleted successfully!'));
    }
}
