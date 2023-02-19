<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends BaseApiController
{
    public function getInfo($accountId)
    {
        if($accountId <= 0 || is_null($accountId)) {
            return $this->sendError('Incorrect accountId', [],400);
        }

        $account = Account::find($accountId);

        if($account) {
            $user = $account->user;

            $response = [
                "id" => $user->id,
                "firstName" => $user->firstName,
                "lastName" => $user->lastName,
                "email" => $user->email,
            ];

            return $this->sendResponse($response, 'Account has been successfully received');
        }

        return $this->sendError('Account with accountId = ' . $accountId . ' not found');
    }

    public function searchUserByAccount(Request $request)
    {
        if($request->from < 0 || $request->size <= 0) {
            return $this->sendError('Incorrect data!');
        }

        $user = User::query()
            ->when($request->get('firstName'), fn ($q, $firstName) => $q->orWhere('firstName', 'LIKE', '%' . $firstName . '%'))
            ->when($request->get('lastName'), fn($q, $lastName) => $q->orWhere('lastName', 'LIKE', '%' . $lastName . '%'))
            ->when($request->get('email'), fn($q, $email) => $q->orWhere('email', 'LIKE', '%' . $email . '%'))
            ->first();

        if($user) {
            $response = [];

            $accounts = $user->accounts()
                ->offset($request->from)
                ->orderBy('id', 'ASC')
                ->take($request->size)
                ->get();

            foreach ($accounts as $account) {
                $response[] = [
                    'id' => $account->id,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email,
                ];
            }

            return $this->sendResponse($response, 'Success!');
        }

        return $this->sendError('Unable to find accounts');
    }
}
