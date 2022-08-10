<?php

namespace App\Services;

use App\Http\Controllers\Api\StudentController;
use App\Models\Teacher;
use App\Models\User;
use App\Utils\Enum\CodeResponse;
use App\Utils\Enum\RoleOrPositions;
use App\Utils\Enum\TextResponse;
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
                case RoleOrPositions::STUDENT:
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

                case RoleOrPositions::TEACHER:
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
                    throw new \Exception(TextResponse::TYPE_USER_NOT_VALID);
            }

            $response['code'] = CodeResponse::CREATED;
            return $response;

        }catch (\Exception $e){
            if($e->getMessage() == TextResponse::TYPE_USER_NOT_VALID){
                return [
                    'code' => CodeResponse::STATUS_OK,
                    'data' => [
                        'message' => TextResponse::TYPE_USER_NOT_VALID
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

                $response['code'] = CodeResponse::STATUS_OK;
                return $response;
            }

            $response = [
                'code' => CodeResponse::UNAUTHORIZED,
                'data' => [
                    'message' => TextResponse::UNAUTHORIZED
                ]
            ];
            return $response;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}