<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function sendResponse($result, $code): \Illuminate\Http\JsonResponse
    {
        if($code == 201 || $code == 200){
            $dataResponse = [
                'data' => $result
            ];
        }else{
            $dataResponse = [];
        }

        return response()->json($dataResponse, $code);
    }

    public function sendError($error, $code): \Illuminate\Http\JsonResponse
    {
        $dataResponse = [
            'message' => $error
        ];

        return response()->json($dataResponse, $code);
    }
}
