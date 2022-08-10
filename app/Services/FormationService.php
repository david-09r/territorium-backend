<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\FormationStudent;
use App\Models\FormationTeacher;
use App\Models\Student;
use App\Models\Teacher;
use App\Utils\Enum\CodeResponse;
use App\Utils\Enum\RoleOrPositions;
use App\Utils\Enum\TextResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Vtiful\Kernel\Format;

class FormationService
{
    public function listFormation()
    {
        try {
            $user = Auth::user();
            $data = null;
            $allData = new Collection();
            if($user['user_type'] == RoleOrPositions::ADMINISTRATOR || $user['user_type'] == RoleOrPositions::SUPERIOR){
                $data = Formation::where('status', true)->get();
            }
            elseif ($user['user_type'] == RoleOrPositions::TEACHER) {
                $teacher = $user->teacher()->first();
                $allData = FormationTeacher::with('formation')
                    ->where('teacher_id', $teacher['id'])->get();
            }
            elseif($user['user_type'] == RoleOrPositions::STUDENT){
                $student = $user->student()->first();
                $allData = FormationStudent::with('formation')
                    ->where('student_id', $student['id'])->get();
            }

            if (is_null($data) && empty($allData->toArray())) {
                return [
                    'data' => 'No tienes lista de formaciones',
                    'code' => CodeResponse::STATUS_OK
                ];
            }

            foreach ($allData as $formation){
                $data[] = $formation['formation'];
            }

            return [
                'data' => $data,
                'code' => CodeResponse::STATUS_OK
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
            if($user['user_type'] == RoleOrPositions::ADMINISTRATOR || $user['user_type'] == RoleOrPositions::SUPERIOR){
                $data = $request->all();
                $data['identification'] = $this->identificationFormation($data);
                $formation = Formation::create($data);
                foreach ($request['teacher'] as $teacherId){
                    $dataTeacher = Teacher::find($teacherId);
                    if(is_null($dataTeacher)){
                        throw new \Exception(TextResponse::NOT_FOUND_USER);
                    }

                    $formation->formationTeachers()->create([
                        'teacher_id' => $teacherId
                    ]);
                }

                foreach ($request['student'] as $studentId){
                    $dataStudent = Student::find($studentId);
                    if(is_null($dataStudent)){
                        throw new \Exception(TextResponse::NOT_FOUND_USER);
                    }

                    $formation->formationStudents()->create([
                        'student_id' => $studentId
                    ]);
                }
            }else {
                throw new \Exception(TextResponse::NOT_PERMISSIONS);
            }

            DB::commit();
            return [
                'data' => [
                    'message' => 'Formacion creada'
                ],
                'code' => CodeResponse::CREATED
            ];
        }catch (\Exception $e){
            DB::rollBack();
            if($e->getMessage() == TextResponse::NOT_PERMISSIONS ) {
                return [
                    'data' => [
                        'message' => TextResponse::NOT_PERMISSIONS
                    ],
                    'code' => CodeResponse::UNAUTHORIZED
                ];
            }elseif($e->getMessage() == TextResponse::NOT_FOUND_USER){
                return [
                    'data' => [
                        'message' => TextResponse::NOT_FOUND_USER
                    ],
                    'code' => CodeResponse::STATUS_OK
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
                case RoleOrPositions::TEACHER:
                    $teacher = $user->teacher()->first();
                    $data = FormationTeacher::with([
                        'formation' => fn($query) => $query->where('name', 'LIKE', "%$formationWord%")->get()
                    ])->where('teacher_id', $teacher['id'])->get();
                    break;

                case RoleOrPositions::STUDENT:
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
                throw new \Exception(CodeResponse::NO_CONTENT);
            }

            return [
                'data' => $formations,
                'code' => CodeResponse::STATUS_OK
            ];

        }catch (\Exception $e){
            if($e->getMessage() == CodeResponse::NO_CONTENT){
                return [
                    'data' => null,
                    'code' => CodeResponse::NO_CONTENT
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