<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use App\Http\Requests\Api\UserRegistrationRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseApiController
{
    public function registration(UserRegistrationRequest $request)
    {
        if($request->has('email')) {
            $existUser = User::where('email', $request->email)->first();

            if($existUser) {
                return $this->sendError('User already exists!', [],409);
            }
        }

        $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Account::create([
            'user_id' => $user->id,
        ]);

        $response = [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
        ];

        return $this->sendResponse($response,"User: $user->firstName was successfuly created!", 201);
    }

    public function login(UserLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user) return $this->sendError('User not found!');

        $token = $user->createToken('token')->plainTextToken;

        return $this->sendResponse($token,'User successfuly loged in!', 201);
    }
}
