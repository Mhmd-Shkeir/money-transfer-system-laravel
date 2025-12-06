<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->query('q')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user_show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user_edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'role' => 'required|in:user,agent,admin',
            'status' => 'required|in:active,suspended',
            'is_email_verified' => 'nullable|boolean',
            'is_phone_verified' => 'nullable|boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user->id)
                       ->with('status', 'User updated successfully.');
    }
}
