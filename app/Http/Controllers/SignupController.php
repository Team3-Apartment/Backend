<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;

class SignupController extends Controller
{
    public function signup()
    {
        $validated = request()->validate([
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6']
        ]);

        $user = User::create([
            'name' => request()->name,
            'email' => request()->email,
            'password' => Hash::make(request()->password)
        ]);
        return response()->json($user);

    }
}
