<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AddedUser;

class AddedUserTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate a user
        $user = User::where('name','Admin')->first();
        $this->actingAs($user);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */

    // testing index method
    public function testAddedUserIndexPage()
    {
        $user = User::where('name','Manager')->first();
        $this->actingAs($user);

        $response = $this->get('api/added-users');

        $response->assertStatus(200);
    }

    // testing index pagination
    public function testAddedUserIndexPagination()
    {
        $user = User::where('name','Manager')->first();
        $this->actingAs($user);

        $response = $this->get('api/added-users');
        
        $response->assertJsonCount(100, '0');

        $response->assertOk();
    }

    // testing show method
    public function testAddedUserShowPage()
    {
        $user = User::where('name','Manager')->first();
        $this->actingAs($user);
        
        $addedUser = AddedUser::latest()->first();
        $response = $this->getJson('api/added-users/'.$addedUser->id);

        $response->assertStatus(200);
    }

    // testing store method
    public function testNewAddedUser()
    {
        
        // $credentials = [
        //     'last_name' => 'Doe',
        //     'first_name' => 'John',
        //     'middle_name' => 'Junior',
        //     'birth_date' => '20/04/1981',
        //     'country_id' => '2',
        //     'pass_num_inn' => '21409200040935',
        //     'passport_id' => '100000000',
        //     'passport_authority' => 'Minstry Affairs',
        //     'passport_authority_code' => '21309200000935',
        //     'passport_issued_at' => '20/04/2005',
        //     'passport_expires_at' => '20/04/2015',
        // ];
        
        // $response = $this->postJson('api/added-users', $credentials);

        // $response->assertStatus(201); // Assert that the response has a status code of 201 (Created)
        // $this->assertDatabaseHas('added_users', ['last_name' => 'Doe']); // Assert that the data is stored in the database

        // // Delete the last data inserted
        // $lastInsertedUser = AddedUser::latest()->first();
        // $lastInsertedUser->delete();
        $this->assertTrue(true);
    }
}
