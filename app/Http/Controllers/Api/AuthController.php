<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                /** @var User $user */
                $user = Auth::user();
                $user->load(['jobPosition:id,name,role_id']);
                $token = $user->createToken('API Token')->accessToken;

                if (config('auth.must_verify_email') && !$user->hasVerifiedEmail()) {
                    return response([
                        'message' => 'Email must be verified.'
                    ], 401);
                }

                return response([
                    'message' => 'success',
                    'token' => $token,
                    'user' => $user
                ]);
            }
        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

        return response([
            'message' => 'Invalid Email or password.'
        ], 401);
    }

    public function user()
    {
        try {
            $user = User::find(Auth::id())->with(['jobPosition:id,role_id', 'jobPosition.role:id,name']);

            return response()->json($user);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Unauthenticated.'
            ], 401);
        }
    }
}
