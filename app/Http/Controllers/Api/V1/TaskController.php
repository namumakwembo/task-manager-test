<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\Api\V1\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
   /**
     * Get all tasks for the authenticated user.
     */
    public function index()
    {
        $tasks = Auth::user()->tasks()->latest()->get();
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created task.
     */
    public function store(TaskRequest $request)
    {
        $task = Auth::user()->tasks()->create($request->validated());

        return new TaskResource($task);
    }

    /**
     * Display a specific task.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    /**
     * Update the specified task.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return new TaskResource($task);
    }

    /**
     * Delete the specified task.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.']);
    }
}
