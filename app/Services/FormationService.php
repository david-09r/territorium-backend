<?php

namespace App\Services;

use App\Models\Formation;
use App\Utils\Enum\CodeResponse;
use Illuminate\Support\Facades\DB;
use Vtiful\Kernel\Format;

class FormationService
{
    public function listFormation()
    {
        try {
            $data = Formation::where('status', true)->get();

            if (count($data) <= 0) {
                return [
                    'data' => 'No tienes lista de formaciones',
                    'code' => CodeResponse::STATUS_OK
                ];
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
            $data = $request->all();
            $data['identification'] = $this->identificationFormation($data);
            Formation::create($data);
            DB::commit();
            return [
                'data' => [
                    'message' => 'Formacion creada'
                ],
                'code' => CodeResponse::CREATED
            ];
        }catch (\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function showFormation($request)
    {
        try {
            $formation = Formation::find($request['id']);

            if(is_null($formation)){
                return [
                    'data' => [],
                    'code' => CodeResponse::NO_CONTENT
                ];
            }

            return [
                'data' => $formation,
                'code' => CodeResponse::STATUS_OK
            ];
        }catch (\Exception $e){
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