<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ErrorResource;

class ReportController extends Controller
{
    public function taskSummary()
    {
        // Get task counts by status
        $taskSummary = Task::selectRaw('status, COUNT(*) as total')
                            ->groupBy('status')
                            ->get();

        // $tasksByUser = User::with('tasks:user_id,title,status,due_date')->get();
        $tasksByUser = User::with('tasks')->get();
        $tasksByUserTransformed = $tasksByUser->map(function ($user) {
            // Wrap user data using UserResource
            // Attach tasks using TaskResource::collection() as an additional field
            return new UserResource($user);
        });

        return response()->json([
            'task_summary' => $taskSummary,
            'tasks_by_user' => $tasksByUserTransformed
        ],200);
    }
    public function userTasks($userId)
    {
        // Fetch tasks assigned to the specific user
        $user = User::find($userId);

        if (!$user) {
            return (new ErrorResource([
                'error' => 'User not found',
                ]))
                ->response()
                ->setStatusCode(404);
        }

        $tasks = $user->tasks()->get();

        // return response()->json([
        //     'user' => $user,
        //     'tasks' => $tasks
        // ],200);
        //Return the user resource with tasks and set additional info
        return (new UserResource($user))
        ->additional([
            'message' => 'User and tasks retrieved successfully',
            'tasks' => TaskResource::collection($user->tasks), // Include tasks with their respective resource
        ])
        ->response() 
        ->setStatusCode(200); 
    }
}
