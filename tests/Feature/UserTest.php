<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate a user
        $user = User::where('name','AdminTester')->first();
        $this->actingAs($user);
    }
    
    // testing index method
    public function testUserIndex()
    {
        $response = $this->get('api/users');

        $response->assertStatus(200);
    }

    // testing show method
    public function testUserShow()
    {
        $addedUser = User::latest()->first();
        $response = $this->getJson('api/users/'.$addedUser->id);

        $response->assertStatus(200);
    }

    // testing store method
    public function testUserStore()
    {  
        $credentials = [
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => 'password123',
            'role' => 'Operator',
            'status' => 0,
        ];
        
        $response = $this->postJson('api/users', $credentials);

        $response->assertStatus(201); // Assert that the response has a status code of 201 (Created)
        $this->assertDatabaseHas('users', ['name' => 'Tester']); // Assert that the data is stored in the database
    
    }
    
    public function testUserStorePasswordLengthFail()
    {  
        $credentials = [
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => 'password',
            'role' => 'Operator',
            'status' => 0,
        ];
        
        $response = $this->postJson('api/users', $credentials);

        $response->assertStatus(400);
    }

    public function testUserStorePasswordLengthPass()
    {  
        $credentials = [
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => '0123456789',
            'role' => 'Operator',
            'status' => 0,
        ];
        
        $response = $this->postJson('api/users', $credentials);

        $response->assertStatus(201);
    }

    public function testUserStoreWrongFields()
    {
        $credentials = [
            // Missing 'name', 'email', 'password', 'role'
        ];

        $response = $this->postJson('api/users', $credentials);

        $response->assertStatus(400);
    }

    public function testUserRoleAssignment()
    {
        $user = User::factory()->create();

        $user->assignRole('Operator');

        $this->assertTrue($user->hasRole('Operator'));
    }

    // testing update method
    public function testUserUpdate()
    {  
        $user = User::factory()->create();

        $newCredentials = [
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => 'password123',
            'role' => 'Manager',
            'status' => 1,
        ];
        
        $response = $this->put('api/users/'.$user->id, $newCredentials);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'role' => 'Manager',
            'status' => 1,
        ]); // Assert that the data is stored in the database
    
    }

    // testing destroy method
    public function testUserDestroy()
    {  
        $user = User::factory()->create();
        
        $response = $this->delete('api/users/'.$user->id);

        $response->assertStatus(204);

        $this->assertModelMissing($user);
    
    }
}
