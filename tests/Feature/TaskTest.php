<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;

class TaskTest extends TestCase
{
    //create task
    public function test_create_task_success()
    {
        // Create a user to associate with the task
        $user = User::factory()->create();
        //authenticate using sanctum
        Sanctum::actingAs($user);
        $response = $this->postJson('/api/tasks', [
            'title' => 'New Task',
            'description' => 'This is a new task description',
            'status' => 'pending',
            'due_date' => '2024-12-31',
            'user_id' => $user->id,
        ]);
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Task created successfully',
            'task' => [
                'title' => 'New Task',
                'description' => 'This is a new task description',
                'status' => 'pending',
                'due_date' => '2024-12-31',
                'user_id' => $user->id,
            ]
        ]);
    }
    public function test_create_task_validation_fail()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        // missing 'title' and invalid 'status'
        $response = $this->postJson('/api/tasks', [
            'description' => 'This is a new task description',
            'status' => 'invalid_status',  // Invalid status
            'due_date' => '2024-12-31',
            'user_id' => $user->id,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'status']);
    }
    //retrieve task
    public function test_filtered_task_by_user_id()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/tasks?user_id=' . $user->id);
        $response->assertStatus(201);
        $response->assertJson([
            'messages' => 'Filtered Tasks',
            'task' => [
                [
                    'user_id' => $user->id,
                ]
            ]
        ]);
    }
    public function test_filtered_task_with_multiple_filters()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create([
            'status' => 'in-progress',
            'due_date' => '2024-12-31',
            'user_id' => $user->id,
        ]);
        $response = $this->getJson('/api/tasks?status=in-progress&due_date=2024-12-31&user_id=' . $user->id);
        $response->assertStatus(201);
        $response->assertJson([
            'messages' => 'Filtered Tasks',
            'task' => [
                [
                    'status' => 'in-progress',
                    'due_date' => '2024-12-31',
                    'user_id' => $user->id,
                ]
            ]
        ]);
    }
    //delete task
    public function test_delete_task_success()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);
        $response = $this->deleteJson('/api/tasks/' . $task->id);
        $response->assertStatus(204);
    }

    public function test_delete_task_not_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->deleteJson('/api/tasks/999'); // Assuming 999 is a non-existent ID
        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'Task not found',
        ]);
    }
}
