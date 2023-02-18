<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends BaseApiController
{
    public function getInfo($accountId)
    {
        if($accountId <= 0 || is_null($accountId)) {
            return $this->sendError('Incorrect accountId', [],400);
        }

        $account = Account::findOrFail($accountId);

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

        return $this->sendError('Account with this accountId not found');
    }
}
