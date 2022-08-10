<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentService
{
    public function __construct(User $user)
    {
        $this->modelUser = $user;
    }

    public function saveStudent(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataPassword = $this->modelUser->generatePasswordAndEncrypt();

            $user = User::create([
                'email' => $request['email'],
                'password' => $dataPassword['passwordEncrypt'],
                'identification_type' => $request['identification_type'],
                'identification_number' => $request['identification_number'],
                'birth_date' => $request['birth_date'],
                'user_type' => $request['user_type']
            ]);

            $token = $user->createToken('MyApp')->plainTextToken;

            $user->student()->create([
                'name' => $request['name'],
                'last_name' => $request['last_name'],
                'phone_number' => $request['phone_number'],
            ]);

            DB::commit();
            return [
                'user' => $user,
                'token' => $token
            ];
        }catch (\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}