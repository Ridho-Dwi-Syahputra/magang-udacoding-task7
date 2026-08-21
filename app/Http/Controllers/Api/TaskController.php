<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->query('q').'%');
        }

        $tasks = $query->latest()->get();

        return response()->json([
            'message' => 'Daftar task berhasil diambil.',
            'total' => $tasks->count(),
            'data' => TaskResource::collection($tasks),
        ]);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'message' => 'Detail task berhasil diambil.',
            'data' => new TaskResource($task),
        ]);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json([
            'message' => 'Task berhasil dibuat.',
            'data' => new TaskResource($task),
        ], 201);
    }

    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
            'message' => 'Task berhasil diperbarui.',
            'data' => new TaskResource($task->fresh()),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'Task berhasil dihapus.',
        ]);
    }
}
