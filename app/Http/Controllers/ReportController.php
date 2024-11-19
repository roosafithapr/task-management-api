<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;

class ReportController extends Controller
{
    public function taskSummary()
    {
        // Get task counts by status
        $taskSummary = Task::selectRaw('status, COUNT(*) as total')
                            ->groupBy('status')
                            ->get();

        $tasksByUser = User::with('tasks:user_id,title,status,due_date')->get();

        return response()->json([
            'task_summary' => $taskSummary,
            'tasks_by_user' => $tasksByUser
        ],200);
    }
    public function userTasks($userId)
    {
        // Fetch tasks assigned to the specific user
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $tasks = $user->tasks()->get();

        return response()->json([
            'user' => $user,
            'tasks' => $tasks
        ],200);
    }
}
