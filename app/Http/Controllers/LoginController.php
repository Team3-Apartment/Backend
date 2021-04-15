<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function login()
    {
        $user = User::where('email', request()->email)
            ->first();

        abort_if(!$user, 400, 'wrong email or password');
        abort_if(!Hash::check( request()->password,$user->password), 400, 'wrong email or password');
        $token = $user->createToken($user->email)->plainTextToken;
        return response()->json([
            'message' => 'you have the correct credintioals',
            'token' => $token
        ]);
    }
}
