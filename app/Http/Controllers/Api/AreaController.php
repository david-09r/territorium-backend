<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Services\AreaService;
use App\Utils\Enum\EnumCodeResponse;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct(AreaService $areaService)
    {
        $this->service = $areaService;
    }

    public function index($formationId)
    {
        try {
            $response = $this->service->listArea($formationId);
            return response()->json(collect(['data' => $response['data']]), $response['code']);
        }catch (\Exception $e){
            return response()->json(collect(['message' => $e->getMessage()]), EnumCodeResponse::INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Area $area)
    {
        //
    }

    public function update(Request $request, Area $area)
    {
        //
    }

    public function destroy(Area $area)
    {
        //
    }
}
