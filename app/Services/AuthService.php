<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use App\Utils\Enum\EnumCodeResponse;
use App\Utils\Enum\EnumRoleOrPositions;
use App\Utils\Enum\EnumTextResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(StudentService $studentService, TeacherService $teacherService)
    {
        $this->studentService = $studentService;
        $this->teacherService = $teacherService;
    }

    public function register($request)
    {
        try {
            switch ($request['user_type']){
                case EnumRoleOrPositions::STUDENT:
                    $data = $this->studentService->saveStudent($request);
                    $user = $data['user'];
                    $response = [
                        'data' => [
                            'message' => 'User Register Successfully',
                            'name' => $user->student->name,
                            'token' => $data['token']
                        ]
                    ];
                    break;

                case EnumRoleOrPositions::TEACHER:
                    $data = $this->teacherService->saveTeacher($request);
                    $user = $data['user'];
                    $response = [
                        'data' => [
                            'message' => 'User Register Successfully',
                            'name' => $user->teacher->name,
                            'token' => $data['token']
                        ]
                    ];
                    break;

                default:
                    throw new \Exception(EnumTextResponse::TYPE_USER_NOT_VALID);
            }

            $response['code'] = EnumCodeResponse::CREATED;
            return $response;

        }catch (\Exception $e){
            if($e->getMessage() == EnumTextResponse::TYPE_USER_NOT_VALID){
                return [
                    'code' => EnumCodeResponse::STATUS_OK,
                    'data' => [
                        'message' => EnumTextResponse::TYPE_USER_NOT_VALID
                    ]
                ];
            }
            throw new \Exception($e->getMessage());
        }
    }

    public function login($request)
    {
        try {
            if(Auth::attempt(['email' => $request['email'], 'password' => $request['password'], 'status' => 1])){
                $user = Auth::user();
                $response = [
                    'data' => [
                        'message' => 'User Login Successfully',
                        'token' => $user->createToken('MyApp')->plainTextToken,
                    ]
                ];

                $response['code'] = EnumCodeResponse::STATUS_OK;
                return $response;
            }

            $response = [
                'code' => EnumCodeResponse::UNAUTHORIZED,
                'data' => [
                    'message' => EnumTextResponse::UNAUTHORIZED
                ]
            ];
            return $response;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}