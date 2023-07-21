<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AddedUser;
use App\Models\LegalEntity;
use App\Models\UserOperation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserOperationTest extends TestCase
{
    use DatabaseTransactions;
    
    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate a user
        $user = User::where('name','AdminTester')->first();
        $this->actingAs($user);
    }
    
    // test index method
    public function testUserOperationIndex()
    {
        $response = $this->get('api/user-operations');

        $response->assertStatus(200);
    }

    // testing index pagination
    public function testUserOperationIndexPagination()
    {
        $response = $this->get('api/user-operations');
        
        $response->assertJsonCount(200, '0');

        $response->assertOk();
    }

    // testing show method
    public function testUserOperationShow()
    {
        $userOperation = UserOperation::latest()->first();
        $response = $this->getJson('api/user-operations/'.$userOperation->id);

        $response->assertStatus(200);
    }

    // testing store method
    public function testUserOperationStoreWithLegalId()
    {  
        $credentials = [
            'operation_date' => '30/11/2023 00:37',
            'operation_sum' => '2',
            'operation_direction' => 'Selling',
            'wallet_id' => '100000000',
            'currency' => 'USD',
            'legal_id' => '1',
        ];
        
        $response = $this->postJson('api/user-operations', $credentials);

        $response->assertStatus(201); // Assert that the response has a status code of 201 (Created)
        $this->assertDatabaseHas('user_operations', [
            'operation_date' => '2023-11-30 00:37',
            'operation_sum' => '200',
            'operation_direction' => 'Selling',
            'wallet_id' => '100000000',
            'currency' => 'USD',
            'legal_id' => '1',
        ]); // Assert that the data is stored in the database
    
    }

    // testing store method
    public function testUserOperationStoreWithUserId()
    {  
        $addedUser = AddedUser::factory()->create(
            [
                'last_name' => 'Doe',
                'first_name' => 'John',
                'middle_name' => 'Junior',
                'birth_date' => '20/04/1981',
                'country_id' => '2',
                'pass_num_inn' => '21409200040835',
                'passport_id' => '100000000',
                'passport_authority' => 'Minstry Affairs',
                'passport_authority_code' => '21309200000935',
                'passport_issued_at' => '20/04/2005',
                'passport_expires_at' => '20/04/2015',
                'verification_date' => '20/04/2015',
                'verification' => '1',
            ]
        );
        
        $credentials = [
            'operation_date' => '30/11/2023 00:37',
            'operation_sum' => '2',
            'operation_direction' => 'Selling',
            'wallet_id' => '100000000',
            'currency' => 'USD',
            'user_id' => $addedUser->id,
        ];
        
        $response = $this->postJson('api/user-operations', $credentials);

        $response->assertStatus(201); // Assert that the response has a status code of 201 (Created)
        $this->assertDatabaseHas('user_operations', [
            'operation_date' => '2023-11-30 00:37',
            'operation_sum' => '200',
            'operation_direction' => 'Selling',
            'wallet_id' => '100000000',
            'currency' => 'USD',
            'user_id' => $addedUser->id,
        ]); // Assert that the data is stored in the database
    }

    // testing store method
    public function testUserOperationStoreFailWhenBothLegalIdAndUserId()
    {  
        $addedUser = AddedUser::factory()->create(
            [
                'last_name' => 'Doe',
                'first_name' => 'John',
                'middle_name' => 'Junior',
                'birth_date' => '20/04/1981',
                'country_id' => '2',
                'pass_num_inn' => '21409200040835',
                'passport_id' => '100000000',
                'passport_authority' => 'Minstry Affairs',
                'passport_authority_code' => '21309200000935',
                'passport_issued_at' => '20/04/2005',
                'passport_expires_at' => '20/04/2015',
                'verification_date' => '20/04/2015',
                'verification' => '1',
            ]
        );
        
        $credentials = [
            'operation_date' => '30/11/2023 00:37',
            'operation_sum' => '2',
            'operation_direction' => 'Selling',
            'wallet_id' => '100000000',
            'currency' => 'USD',
            'legal_id' => '1',
            'user_id' => $addedUser->id,
        ];
        
        $response = $this->postJson('api/user-operations', $credentials);

        $response->assertStatus(422); // Error when both legal_id and user_id are used
        $this->assertDatabaseMissing('user_operations', [
            'legal_id' => '1',
            'user_id' => $addedUser->id,
        ]); // Assert that the data is not stored in the database
    }

    // testing update method
    public function testUserOperationUpdateWithUserId()
    {  
        $addedUser = AddedUser::factory()->create(
            [
                'last_name' => 'Doe',
                'first_name' => 'John',
                'middle_name' => 'Junior',
                'birth_date' => '20/04/1981',
                'country_id' => '2',
                'pass_num_inn' => '21409200040835',
                'passport_id' => '100000000',
                'passport_authority' => 'Minstry Affairs',
                'passport_authority_code' => '21309200000935',
                'passport_issued_at' => '20/04/2005',
                'passport_expires_at' => '20/04/2015',
                'verification_date' => '20/04/2015',
                'verification' => '1',
            ]
        );

        $userOperation = UserOperation::factory()->create(
            [
                'operation_date' => '30/11/2023 00:37',
                'operation_sum' => '2',
                'operation_direction' => 'Selling',
                'wallet_id' => '100000000',
                'currency' => 'USD',
                'user_id' => $addedUser->id,
            ]
        );

        $newCredentials = [
            'operation_date' => '30/11/2023 00:37',
            'operation_sum' => '3',
            'operation_direction' => 'Buying',
            'wallet_id' => '100000000',
            'currency' => 'KGS',
            'user_id' => $addedUser->id,
        ];
        
        $response = $this->put('api/user-operations/'.$userOperation->id, $newCredentials);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_operations', [
            'operation_date' => '2023-11-30 00:37',
            'operation_sum' => '300',
            'operation_direction' => 'Buying',
            'wallet_id' => '100000000',
            'currency' => 'KGS',
            'user_id' => $addedUser->id,
        ]); // Assert that the data is stored in the database
    }

    // testing update method
    public function testUserOperationUpdateWithLegalId()
    {  
        $userOperation = UserOperation::create(
            [
                'operation_date' => '30/11/2023 00:37',
                'operation_sum' => '2',
                'operation_direction' => 'Selling',
                'wallet_id' => '100000000',
                'currency' => 'USD',
                'legal_id' => '1',
            ]
        );

        $newCredentials = [
            'operation_date' => '30/11/2023 00:37',
            'operation_sum' => '3',
            'operation_direction' => 'Buying',
            'wallet_id' => '100000000',
            'currency' => 'KGS',
            'legal_id' => '1',
        ];
        
        $response = $this->put('api/user-operations/'.$userOperation->id, $newCredentials);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_operations', [
            'operation_date' => '2023-11-30 00:37',
            'operation_sum' => '300',
            'operation_direction' => 'Buying',
            'wallet_id' => '100000000',
            'currency' => 'KGS',
            'legal_id' => '1',
        ]); // Assert that the data is stored in the database
    }

    // testing destroy method
    public function testUserOperationDestroy()
    {  
        $userOperation = UserOperation::create(
            [
                'operation_date' => '30/11/2023 00:37',
                'operation_sum' => '2',
                'operation_direction' => 'Selling',
                'wallet_id' => '100000000',
                'currency' => 'USD',
                'legal_id' => '1',
            ]
        );
        
        $response = $this->delete('api/user-operations/'.$userOperation->id);

        $response->assertStatus(204);

        $this->assertModelMissing($userOperation); // Assert that the data is not stored in the database
    }

    // testing search method with user
    public function testUserOperationSearchWithUser()
    {  
        $addedUser = AddedUser::factory()->create(
            [
                'last_name' => 'SearchedDoe',
                // 'first_name' => 'John',
                // 'middle_name' => 'Junior',
                'birth_date' => '20/04/1981',
                // 'country_id' => '2',
                // 'pass_num_inn' => '21409200040935',
                // 'passport_id' => '100000000',
                // 'passport_authority' => 'Minstry Affairs',
                // 'passport_authority_code' => '21309200000935',
                'passport_issued_at' => '20/04/2005',
                'passport_expires_at' => '20/04/2015',
                'verification_date' => '20/04/2015',
                // 'verification' => '1',
            ]
        );

        $userOperation = UserOperation::factory(3)->create(
            [
                'operation_date' => '30/11/2023 00:37',
                'operation_sum' => '2',
                'operation_direction' => 'Selling',
                'wallet_id' => '100000000',
                'currency' => 'USD',
                'user_id' => $addedUser->id,
            ]
        );
        
        $credentialsToSearch = [
            'type' => 'user',
            'name' => 'SearchedDoe',
        ];
        
        $response = $this->postJson('api/user-operations/search', $credentialsToSearch);

        $response->assertStatus(200);
        $response->assertJson([
            [
                [
                    "added_user" => [
                        "last_name" => "SearchedDoe",
                    ]
                ]
            ]
        ]);
    }

    // testing search method with legal
    public function testUserOperationSearchWithLegal()
    {  
        $legalEntity = LegalEntity::create(
            [
                'name' => 'TestCompany',
                'director_full_name' => 'John Doe',
                'birth_date' => '20/04/1981',
                'country_id' => '2',
                'address' => 'John Street 3',
            ]
        );
        
        $userOperation = UserOperation::create(
            [
                'operation_date' => '30/11/2023 00:37',
                'operation_sum' => '2',
                'operation_direction' => 'Selling',
                'wallet_id' => '100000000',
                'currency' => 'USD',
                'legal_id' => $legalEntity->id,
            ]
        );
        
        $credentialsToSearch = [
            'type' => 'legal',
            'name' => 'TestCompany',
        ];
        
        $response = $this->postJson('api/user-operations/search', $credentialsToSearch);

        $response->assertStatus(200);
        
        $response->assertJson([
            [
                [
                    "legal_entity" => [
                        "name" => "TestCompany",
                    ]
                ]
            ]
        ]);
    }
}
