<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message, $code = 200)
    {
        $response = [
            'success' => true,
            'body'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'error' => true,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['body'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
