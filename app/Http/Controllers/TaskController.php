<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;

class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in-progress,completed',
            'due_date' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
        ]);
        // Ensure user exists
        $user = User::find($validated['user_id']);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $task = Task::create($validated);
        return response()->json(
            ['message' => 'Task created successfully', 'task' => $task], 201);
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
            return response()->json(['message' => 'No tasks found'], 404);
        }

        return response()->json(
            ['messages' => 'Filtered Tasks', 'task' => $tasks], 201);
    }

    public function showTask($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        return response()->json([
            'message' => 'Task retrieved successfully',
            'data' => $task,
         ],200);
    }
    public function updateTask(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in-progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);
    }
    public function deleteTask($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ], 204);
    }
    
}
