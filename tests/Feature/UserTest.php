<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    //user creation
    public function test_user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'securePassword123',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'New user registered',
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }
    public function test_registration_fails_when_email_is_invalid()
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Jane Doe',
            'email' => 'invalid-email', // Invalid email
            'password' => 'securePassword123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
    public function test_registration_fails_when_email_is_not_unique()
    {
        User::factory()->create([
            'email' => 'jane@example.com',
        ]);

        $response = $this->postJson('/api/users', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com', // Duplicate email
            'password' => 'securePassword123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
    //user retrieval
    public function test_show_user_success()
    {
        // Create a test user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);
        //authenticate using sanctum
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/users/' . $user->id);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
            ],
        ]);
    }
    public function test_show_user_not_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        // Try to get a user with a non-existing ID
        $response = $this->getJson('/api/users/999');  // Assume 999 is a non-existent user ID
        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'User not found',
        ]);
    }
    //update user
    public function test_update_user_success()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);
        Sanctum::actingAs($user);
        $response = $this->putJson('/api/users/' . $user->id, [
            'name' => 'Updated Name',
            'email' => 'updatedemail@example.com', // New valid email
        ]);
        //dd($response->json());
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => 'Updated Name',
                'email' => 'updatedemail@example.com',
            ],
        ]);
    }
    public function test_update_user_unauthorized()
    {
        // Create a user but don't authenticate them
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);
        $response = $this->putJson('/api/users/' . $user->id, [
            'name' => 'Updated Name',
            'email' => 'updatedemail@example.com',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
    //delete user
    public function test_delete_user_success()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->deleteJson('/api/users/' . $user->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
    public function test_delete_user_not_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->deleteJson('/api/users/999');  // Non-existing user ID
        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'User not found',
        ]);
    }

}
