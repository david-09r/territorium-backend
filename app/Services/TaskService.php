<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Formation;
use App\Models\FormationStudent;
use App\Models\FormationTeacher;
use App\Models\Task;
use App\Models\Teacher;
use App\Models\User;
use App\Utils\Enum\CodeResponse;
use App\Utils\Enum\RoleOrPositions;
use App\Utils\Enum\TextResponse;
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
                case RoleOrPositions::STUDENT:
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
                        $data = TextResponse::NOT_DATA_FORMATION;
                    }
                    break;

                case RoleOrPositions::TEACHER:
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
                        $data = TextResponse::NOT_DATA_FORMATION;
                    }
                    break;

                default:
                    $data = Area::where('formation_id', $formation_id)
                        ->with('tasks')
                        ->get();
            }

            return [
                'data' => $data,
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

    public function saveTask($data)
    {
        try {
            $user = Auth::user();
            switch ($user['user_type']) {
                case RoleOrPositions::TEACHER:
                    $teacher = $user->teacher()->first();
                    $areaId = Area::where('id', $data['area_id'])
                        ->where('teacher_id',  $teacher['id'])
                        ->first();

                    if(is_null($areaId)){
                        throw new \Exception(TextResponse::NOT_EXIST_AREA);
                    }
                    break;

                case RoleOrPositions::STUDENT:
                    throw new \Exception(TextResponse::NOT_PERMISSIONS);
                default:
                    $areaId = Area::find($data['area_id']);
            }

            $data = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'area_id' => $areaId['id']
            ]);

            return [
                'data' => $data,
                'code' => CodeResponse::CREATED
            ];
        }catch (\Exception $e){
            if($e->getMessage() == TextResponse::NOT_EXIST_AREA){
                return [
                    'data' => TextResponse::NOT_EXIST_AREA,
                    'code' => CodeResponse::STATUS_OK
                ];
            }else if($e->getMessage() == TextResponse::NOT_PERMISSIONS){
                return [
                    'data' => TextResponse::NOT_PERMISSIONS,
                    'code' => CodeResponse::STATUS_OK
                ];
            }
            throw new \Exception($e->getMessage());
        }
    }
}