<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Formation;
use App\Models\FormationStudent;
use App\Models\FormationTeacher;
use App\Models\Task;
use App\Models\User;
use App\Utils\Enum\EnumCodeResponse;
use App\Utils\Enum\EnumRoleOrPositions;
use App\Utils\Enum\EnumTextResponse;
use Illuminate\Support\Facades\Auth;

class AreaService
{
    public function __construct(Task $task)
    {
        $this->modelTask = $task;
    }

    public function listArea($formation_id)
    {
        try {
            $user = Auth::user();
            switch ($user['user_type']){
                case EnumRoleOrPositions::STUDENT:
                    $student = $user->student()->first();
                    $verifyFormation = FormationStudent::where('student_id', $student['id'])
                        ->where('formation_id', $formation_id)->get();

                    if(!$verifyFormation->isEmpty()){
                        $formationsWithArea = Area::where('formation_id', $formation_id)
                            ->get();
                    }else {
                        throw new \Exception(EnumTextResponse::NOT_PERMISSIONS);
                    }
                    break;

                case EnumRoleOrPositions::TEACHER:
                    $teacher = $user->teacher()->first();
                    $verifyFormation = FormationTeacher::where('teacher_id', $teacher['id'])
                        ->where('formation_id', $formation_id)->get();

                    if(!$verifyFormation->isEmpty()){
                        $formationsWithArea = Area::where('formation_id', $formation_id)
                            ->where('teacher_id', $teacher['id'])
                            ->get();
                    }else {
                        throw new \Exception(EnumTextResponse::NOT_PERMISSIONS);
                    }
                    break;
                default:
                    $formationsWithArea = Area::where('status', true)->get();
            }

            if($formationsWithArea->isEmpty()){
                throw new \Exception(EnumCodeResponse::NO_CONTENT);
            }

            return [
                'data' => $formationsWithArea,
                'code' => EnumCodeResponse::STATUS_OK
            ];

        }catch (\Exception $e){
            if($e->getMessage() == EnumCodeResponse::NO_CONTENT){
                return [
                    'data' => null,
                    'code' => EnumCodeResponse::NO_CONTENT
                ];
            }elseif ($e->getMessage() == EnumTextResponse::NOT_PERMISSIONS){
                return [
                    'data' => EnumTextResponse::NOT_PERMISSIONS,
                    'code' => EnumCodeResponse::STATUS_OK
                ];
            }
            throw new \Exception($e->getMessage());
        }
    }
}