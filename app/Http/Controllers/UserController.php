<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    public function showMe()
    {
        return response()->json(Auth::user());
    }


    public function destroy()
    {
        abort_if(!Hash::check(\request()->password , Auth::user()->password) , 401, "invalid password");
        $authed_user = Auth::user();
        // delete all access tokens
        $authed_user->tokens()->delete();
        $authed_user->delete();

        return response()->json([]);
    }

    public function update()
    {
        request()->validate([
            'current_password' => 'required',
            'email' => 'email',
            'name' => 'min:5',
            'new_password' => ['min:6', 'same:new_password_confirmation'],
            'new_password_confirmation' => ['min:6', 'required_with:new_password']
        ]);

        $authed_user = Auth::user();

        if (!Hash::check(request()->current_password, $authed_user->password)) {
            return response('current password is wrong', 401);
        }

        if (request()->email) {
            $user_with_email = User::where(['email' => request()->email])->first();
            if ($user_with_email && $user_with_email->id != $authed_user->id) {
                return response('Email already in use', 400);
            }

            $authed_user->email = request()->email;
        }
        if (request()->new_password) {
            $authed_user->password = Hash::make(request()->new_password);
        }

        if (request()->name) {
            $authed_user->name = request()->name;
        }
        $authed_user->save();
        return [
            'user' => $authed_user
        ];
    }


}
