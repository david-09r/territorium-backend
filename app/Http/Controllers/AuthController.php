<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ResponseController;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use App\Utils\Enum\CodeResponse;
use Illuminate\Http\Request;

class AuthController extends ResponseController
{
    public function __construct(AuthService $authService)
    {
        $this->service = $authService;
    }

    public function register(AuthRegisterRequest $request)
    {
        try {
            $response = $this->service->register($request);
            return $this->sendResponse($response['data'], $response['code']);
        }catch (\Exception $e){
            return $this->sendError($e->getMessage(), CodeResponse::INTERNAL_SERVER_ERROR);
        }
    }

    public function login(AuthLoginRequest $request)
    {
        try {
            $response = $this->service->login($request);
            return $this->sendResponse($response['data'], $response['code']);
        }catch (\Exception $e){
            return $this->sendError($e->getMessage(), CodeResponse::INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return $this->sendResponse(['message' => 'Logged Out'], CodeResponse::STATUS_OK);
    }
}
