<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\ErrorResource;

class TaskController extends Controller
{
    public function createTask(TaskRequest $request)
    {
        $validated = $request->validated();
        // Ensure user exists
        $user = User::find($validated['user_id']);
        if (!$user) {
            return (new ErrorResource([
                'error' => 'User not found',
                ]))
                ->response()
                ->setStatusCode(404);
        }
        $task = Task::create($validated);

        // return response()->json(
        //     ['message' => 'Task created successfully', 'task' => $task], 201);

        return (new TaskResource($task))
        ->additional([
            'message' => 'Task created successfully',
        ])
        ->response()
        ->setStatusCode(201);
    }

    public function filteredTask(Request $request)
    {
        $query = Task::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $tasks = $query->get();

        if ($tasks->isEmpty()) {
            return (new ErrorResource([
                'error' => 'No tasks found',
                ]))
                ->response()
                ->setStatusCode(404);
        }

        // return response()->json(
        //     ['messages' => 'Filtered Tasks', 'task' => $tasks], 201);

        return TaskResource::collection($tasks)
        ->additional([
            'message' => 'Filtered Tasks',
        ])
        ->response()
        ->setStatusCode(201);
    }

    public function showTask($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return (new ErrorResource([
                'error' => 'No tasks found',
                ]))
                ->response()
                ->setStatusCode(404);
        }

        // return response()->json([
        //     'message' => 'Task retrieved successfully',
        //     'data' => $task,
        //  ],200);

        return (new TaskResource($task))
        ->additional([
            'message' => 'Task retrieved successfully',
        ])
        ->response()
        ->setStatusCode(200);
    }
    public function updateTask(TaskRequest $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return (new ErrorResource([
                'error' => 'No tasks found',
                ]))
                ->response()
                ->setStatusCode(404);
        }

        $validated = $request->validated();

        $task->update($validated);

        // return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);

        return (new TaskResource($task))
        ->additional([
            'message' => 'Task updated successfully',
        ])
        ->response()
        ->setStatusCode(200);
    }
    public function deleteTask($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return (new ErrorResource([
                'error' => 'No tasks found',
                ]))
                ->response()
                ->setStatusCode(404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ], 204);
    }
    
}
