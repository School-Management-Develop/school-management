<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $statTotal = User::count();

        return view('backend.page.users.index', compact('users', 'statTotal', 'q'));
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff'],
            'status' => ['required', 'in:0,1'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data['role'],
            'status' => (int) $data['status'],
        ]);

        return back()->with('success', __('app.User created successfully.'));
    }

    public function update(Request $request, User $user)
    {
        // ✅ FIX: define $data first
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff'],
            'status' => ['required', 'in:0,1'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->status = (int) $data['status'];

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        return back()->with('success', __('app.User updated successfully.'));
    }

    public function destroy(User $user)
    {
        // prevent deleting yourself
        //     if (auth()->check() && auth()->id() === $user->id) {
        //     return back()->withErrors(['delete' => 'You cannot delete your own account.']);
        //  }

        $user->delete();

        return back()->with('success', 'app.User deleted successfully.');
    }
    public function updateTelegram(Request $request, User $user)
    {
        if (!in_array(strtolower($user->role ?? ''), ['admin', 'super admin', 'superadmin'])) {
            return back()->withErrors(['error' => __('app.Only admin users can have Telegram contacts.')]);
        }

        $request->validate([
            'telegram_username' => 'required|string|max:255',
        ]);

        $user->update([
            'telegram_username' => ltrim($request->telegram_username, '@'),
        ]);

        return back()->with('success', __('app.Telegram updated successfully.'));
    }

    public function removeTelegram(User $user)
    {
        $user->update(['telegram_username' => null]);

        return back()->with('success', __('app.Telegram removed successfully.'));
    }
}
