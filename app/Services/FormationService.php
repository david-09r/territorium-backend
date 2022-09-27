<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\FormationStudent;
use App\Models\FormationTeacher;
use App\Models\Student;
use App\Models\Teacher;
use App\Utils\Enum\EnumCodeResponse;
use App\Utils\Enum\EnumRoleOrPositions;
use App\Utils\Enum\EnumTextResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormationService
{
    public function listFormation()
    {
        try {
            $user = Auth::user();
            $data = null;
            $allData = new Collection();
            if($user['user_type'] == EnumRoleOrPositions::ADMINISTRATOR || $user['user_type'] == EnumRoleOrPositions::SUPERIOR){
                $data = Formation::where('status', true)->get();
            }
            elseif ($user['user_type'] == EnumRoleOrPositions::TEACHER) {
                $teacher = $user->teacher()->first();
                $allData = FormationTeacher::with('formation')
                    ->where('teacher_id', $teacher['id'])->get();
            }
            elseif($user['user_type'] == EnumRoleOrPositions::STUDENT){
                $student = $user->student()->first();
                $allData = FormationStudent::with('formation')
                    ->where('student_id', $student['id'])->get();
            }

            if (is_null($data) && empty($allData->toArray())) {
                return [
                    'data' => 'No tienes lista de formaciones',
                    'code' => EnumCodeResponse::STATUS_OK
                ];
            }

            foreach ($allData as $formation){
                $data[] = $formation['formation'];
            }

            return [
                'data' => $data,
                'code' => EnumCodeResponse::STATUS_OK
            ];

        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function storeFormation($request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            if($user['user_type'] == EnumRoleOrPositions::ADMINISTRATOR || $user['user_type'] == EnumRoleOrPositions::SUPERIOR){
                $data = $request->all();
                $data['identification'] = $this->identificationFormation($data);
                $formation = Formation::create($data);
                foreach ($request['teacher'] as $teacherId){
                    $dataTeacher = Teacher::find($teacherId);
                    if(is_null($dataTeacher)){
                        throw new \Exception(EnumTextResponse::NOT_FOUND_USER);
                    }

                    $formation->formationTeachers()->create([
                        'teacher_id' => $teacherId
                    ]);
                }

                foreach ($request['student'] as $studentId){
                    $dataStudent = Student::find($studentId);
                    if(is_null($dataStudent)){
                        throw new \Exception(EnumTextResponse::NOT_FOUND_USER);
                    }

                    $formation->formationStudents()->create([
                        'student_id' => $studentId
                    ]);
                }
            }else {
                throw new \Exception(EnumTextResponse::NOT_PERMISSIONS);
            }

            DB::commit();
            return [
                'data' => [
                    'message' => 'Formacion creada'
                ],
                'code' => EnumCodeResponse::CREATED
            ];
        }catch (\Exception $e){
            DB::rollBack();
            if($e->getMessage() == EnumTextResponse::NOT_PERMISSIONS ) {
                return [
                    'data' => [
                        'message' => EnumTextResponse::NOT_PERMISSIONS
                    ],
                    'code' => EnumCodeResponse::UNAUTHORIZED
                ];
            }elseif($e->getMessage() == EnumTextResponse::NOT_FOUND_USER){
                return [
                    'data' => [
                        'message' => EnumTextResponse::NOT_FOUND_USER
                    ],
                    'code' => EnumCodeResponse::STATUS_OK
                ];
            }
            throw new \Exception($e->getMessage());
        }
    }

    public function showFormation($formationWord)
    {
        try {
            $user = Auth::user();
            switch ($user['user_type']) {
                case EnumRoleOrPositions::TEACHER:
                    $teacher = $user->teacher()->first();
                    $data = FormationTeacher::with([
                        'formation' => fn($query) => $query->where('name', 'LIKE', "%$formationWord%")->get()
                    ])->where('teacher_id', $teacher['id'])->get();
                    break;

                case EnumRoleOrPositions::STUDENT:
                    $student = $user->student()->first();
                    $data = FormationStudent::with([
                        'formation' => fn($query) => $query->where('name', 'LIKE', "%$formationWord%")->get()
                    ])->where('student_id', $student['id'])->get();
                    break;

                default:
                    $data = Formation::where('name', 'LIKE', "%$formationWord%")->get();
            }

            foreach ($data as $formation){
                if(!is_null($formation['formation'])){
                    $formations[] = $formation['formation'];
                }
            }

            if(!isset($formations)){
                throw new \Exception(EnumCodeResponse::NO_CONTENT);
            }

            return [
                'data' => $formations,
                'code' => EnumCodeResponse::STATUS_OK
            ];

        }catch (\Exception $e){
            if($e->getMessage() == EnumCodeResponse::NO_CONTENT){
                return [
                    'data' => null,
                    'code' => EnumCodeResponse::NO_CONTENT
                ];
            }
            throw new \Exception($e->getMessage());
        }
    }

    private function identificationFormation($data)
    {
        try {
            $existFormation = true;
            while($existFormation){
                $number = random_int(999999, 9999999);
                $data['identification'] = $data['name'] ."-$number";
                $formation = Formation::where('identification', $data['identification'])->first();

                if (is_null($formation)){
                    $existFormation = false;
                }else {
                    $existFormation = true;
                }

            }

            return $data['identification'];
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

}