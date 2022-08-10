<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Formation;
use App\Models\FormationStudent;
use App\Models\FormationTeacher;
use App\Models\Task;
use App\Models\User;
use App\Utils\Enum\CodeResponse;
use App\Utils\Enum\RoleOrPositions;
use App\Utils\Enum\TextResponse;
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
                case RoleOrPositions::STUDENT:
                    $student = $user->student()->first();
                    $verifyFormation = FormationStudent::where('student_id', $student['id'])
                        ->where('formation_id', $formation_id)->get();

                    if(!$verifyFormation->isEmpty()){
                        $formationsWithArea = Area::where('formation_id', $formation_id)
                            ->get();
                    }else {
                        throw new \Exception(TextResponse::NOT_PERMISSIONS);
                    }
                    break;

                case RoleOrPositions::TEACHER:
                    $teacher = $user->teacher()->first();
                    $verifyFormation = FormationTeacher::where('teacher_id', $teacher['id'])
                        ->where('formation_id', $formation_id)->get();

                    if(!$verifyFormation->isEmpty()){
                        $formationsWithArea = Area::where('formation_id', $formation_id)
                            ->where('teacher_id', $teacher['id'])
                            ->get();
                    }else {
                        throw new \Exception(TextResponse::NOT_PERMISSIONS);
                    }
                    break;
                default:
                    $formationsWithArea = Area::where('status', true)->get();
            }

            if($formationsWithArea->isEmpty()){
                throw new \Exception(CodeResponse::NO_CONTENT);
            }

            return [
                'data' => $formationsWithArea,
                'code' => CodeResponse::STATUS_OK
            ];

        }catch (\Exception $e){
            if($e->getMessage() == CodeResponse::NO_CONTENT){
                return [
                    'data' => null,
                    'code' => CodeResponse::NO_CONTENT
                ];
            }elseif ($e->getMessage() == TextResponse::NOT_PERMISSIONS){
                return [
                    'data' => TextResponse::NOT_PERMISSIONS,
                    'code' => CodeResponse::STATUS_OK
                ];
            }
            throw new \Exception($e->getMessage());
        }
    }
}