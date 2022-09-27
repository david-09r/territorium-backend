<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Formation;
use App\Models\FormationStudent;
use App\Models\FormationTeacher;
use App\Models\Task;
use App\Models\Teacher;
use App\Models\User;
use App\Utils\Enum\EnumCodeResponse;
use App\Utils\Enum\EnumRoleOrPositions;
use App\Utils\Enum\EnumTextResponse;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    public function __construct(Task $task)
    {
        $this->modelTask = $task;
    }

    public function listTaskAll($formation_id)
    {
        try {
            $user = Auth::user();
            switch ($user['user_type']){
                case EnumRoleOrPositions::STUDENT:
                    $student = $user->student()->first();
                    $verifyFormation = FormationStudent::where('student_id', $student['id'])
                        ->where('formation_id', $formation_id)
                        ->where('status', true)
                        ->get();

                    if(!$verifyFormation->isEmpty()){
                        $data = Area::where('formation_id', $formation_id)
                            ->with('tasks')
                            ->get();

                    }else {
                        $data = EnumTextResponse::NOT_DATA_FORMATION;
                    }
                    break;

                case EnumRoleOrPositions::TEACHER:
                    $teacher = $user->teacher()->first();
                    $verifyFormation = FormationTeacher::where('formation_id', $formation_id)
                        ->where('teacher_id', $teacher['id'])
                        ->where('status', true)
                        ->get();

                    if(!$verifyFormation->isEmpty()){
                        $data = Area::where('formation_id', $formation_id)
                            ->with('tasks')
                            ->get();
                    }else {
                        $data = EnumTextResponse::NOT_DATA_FORMATION;
                    }
                    break;

                default:
                    $data = Area::where('formation_id', $formation_id)
                        ->with('tasks')
                        ->get();
            }

            return [
                'data' => $data,
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

    public function saveTask($data)
    {
        try {
            $user = Auth::user();
            switch ($user['user_type']) {
                case EnumRoleOrPositions::TEACHER:
                    $teacher = $user->teacher()->first();
                    $areaId = Area::where('id', $data['area_id'])
                        ->where('teacher_id',  $teacher['id'])
                        ->first();

                    if(is_null($areaId)){
                        throw new \Exception(EnumTextResponse::NOT_EXIST_AREA);
                    }
                    break;

                case EnumRoleOrPositions::STUDENT:
                    throw new \Exception(EnumTextResponse::NOT_PERMISSIONS);
                default:
                    $areaId = Area::find($data['area_id']);
            }

            if(is_null($areaId)) {
                throw new \Exception(EnumTextResponse::NOT_EXIST_AREA);
            }

            $data = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'area_id' => $areaId['id']
            ]);

            return [
                'data' => $data,
                'code' => EnumCodeResponse::CREATED
            ];
        }catch (\Exception $e){
            if($e->getMessage() == EnumTextResponse::NOT_EXIST_AREA){
                return [
                    'data' => EnumTextResponse::NOT_EXIST_AREA,
                    'code' => EnumCodeResponse::STATUS_OK
                ];
            }else if($e->getMessage() == EnumTextResponse::NOT_PERMISSIONS){
                return [
                    'data' => EnumTextResponse::NOT_PERMISSIONS,
                    'code' => EnumCodeResponse::STATUS_OK
                ];
            }
            throw new \Exception($e->getMessage());
        }
    }
}