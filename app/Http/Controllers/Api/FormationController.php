<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormationRequest;
use App\Http\Requests\FormationShowRequest;
use App\Models\Formation;
use App\Services\FormationService;
use App\Utils\Enum\CodeResponse;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function __construct(FormationService $formationService)
    {
        $this->service = $formationService;
    }

    public function index()
    {
        try {
            $response = $this->service->listFormation();
            return response()->json(collect(['data' => $response['data']]), $response['code']);
        }catch (\Exception $e){
            return response()->json(collect(['message' => $e->getMessage()]), CodeResponse::INTERNAL_SERVER_ERROR);
        }
    }

    public function store(FormationRequest $request)
    {
        try {
            $request->validated();
            $response = $this->service->storeFormation($request);
            return response()->json(collect(['data' => $response['data']]), $response['code']);
        } catch (\Exception $e) {
            return response()->json(collect(['message' => $e->getMessage()]), CodeResponse::INTERNAL_SERVER_ERROR);
        }
    }

    public function show(FormationShowRequest $request)
    {
        try {
            $request->validated();
            $response = $this->service->showFormation($request);
            return response()->json(collect(['data' => $response['data']]), $response['code']);
        }catch (\Exception $e){
            return response()->json(collect(['message' => $e->getMessage()]), CodeResponse::INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, Formation $formation)
    {
        //
    }

    public function destroy(Formation $formation)
    {
        //
    }
}
