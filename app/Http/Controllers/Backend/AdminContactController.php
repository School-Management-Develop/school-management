<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AdminContact;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function index()
    {
        $contacts = AdminContact::latest()->get();
        return view('backend.page.admin-contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'telegram_username' => 'required|string|max:255',
        ]);

        AdminContact::create([
            'name' => $request->name,
            'telegram_username' => ltrim($request->telegram_username, '@'),
            'is_active' => true,
        ]);

        return back()->with('success', 'Contact added successfully.');
    }

    public function update(Request $request, AdminContact $contact)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'telegram_username' => 'required|string|max:255',
        ]);

        $contact->update([
            'name' => $request->name,
            'telegram_username' => ltrim($request->telegram_username, '@'),
        ]);

        return back()->with('success', 'Contact updated successfully.');
    }

    public function toggleStatus(AdminContact $contact)
    {
        $contact->update(['is_active' => !$contact->is_active]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function destroy(AdminContact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Contact deleted successfully.');
    }
}