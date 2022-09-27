<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use App\Utils\Enum\EnumCodeResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(TaskService $_service)
    {
        $this->service = $_service;
    }

    public function index($formation_id)
    {
        try {
            $response = $this->service->listTaskAll($formation_id);
            return response()->json(collect(['data' => $response['data']]), $response['code']);
        }catch (\Exception $e){
            return response()->json(collect(['message' => $e->getMessage()]), EnumCodeResponse::INTERNAL_SERVER_ERROR);
        }
    }

    public function store(TaskRequest $request)
    {
        try {
            $data = $request->validated();
            $response = $this->service->saveTask($data);
            return response()->json(collect(['data' => $response['data']]), $response['code']);
        }catch (\Exception $e){
            return response()->json(collect(['message' => $e->getMessage()]), EnumCodeResponse::INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Task $task)
    {
        //
    }

    public function update(Request $request, Task $task)
    {
        //
    }

    public function destroy(Task $task)
    {
        //
    }
}
