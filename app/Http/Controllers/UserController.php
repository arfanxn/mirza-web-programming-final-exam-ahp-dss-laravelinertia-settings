<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function showSelf(Request $request)
    {
        $user = Auth::user();
        return Inertia::render('Users/ShowSelf', compact('user'));
    }


    public function editSelf(Request $request)
    {
        $user = Auth::user();
        return Inertia::render('Users/EditSelf', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['nullable', 'min:2'],
            'email' => ['nullable', 'email'],
            'current_password' => ['nullable', 'min:8'],
            'new_password' => ['nullable', 'min:8'],
            'new_password_confirmation' => ['nullable', 'same:new_password'],
        ]);

        $user = User::query()->where('id', $id)->first();
        $user->name = $request->get('name', $user->name);
        $user->email_verified_at = $request->email == $user->email ? $user->email_verified_at : null; // reset email verified at if the email is changed
        $user->email = $request->get('email', $user->email);
        if (isset($request->new_password) && $request->new_password != "") {
            if (isset($user->password) && !Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect']);
            }
            $user->password = isset($request->new_password) ? bcrypt($request->new_password) : $user->password;
        }
        $user->save();

        return redirect()->back()->with('message', 'Update user successful.');
    }
}
