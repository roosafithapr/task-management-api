<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
class ReportTest extends TestCase
{
    public function test_task_summary_success()
    {
        // Step 1: Create test users and tasks
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);
        Sanctum::actingAs($user2);
        Task::factory()->create([
            'user_id' => $user1->id,
            'status' => 'pending',
        ]);
        Task::factory()->create([
            'user_id' => $user1->id,
            'status' => 'completed',
        ]);
        Task::factory()->create([
            'user_id' => $user2->id,
            'status' => 'in-progress',
        ]);
        Sanctum::actingAs($user1);
        $response = $this->getJson('/api/reports/task-summary');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'task_summary' => [
                '*' => [
                    'status',
                    'total',
                ],
            ],
            'tasks_by_user' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'tasks' => [
                        '*' => [
                            'user_id',
                            'title',
                            'status',
                            'due_date',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_user_tasks_success()
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/reports/user-tasks/' . $user->id);
        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'tasks' => $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'due_date' => $task->due_date,
                    'user_id' => $task->user_id,
                ];
            })->toArray(),
        ]);
    }

    public function test_user_tasks_user_not_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/reports/user-tasks/999'); // Assume 999 is a non-existent user ID
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'User not found',
        ]);
    }

}
