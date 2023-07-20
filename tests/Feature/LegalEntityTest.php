<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\LegalEntity;
use App\Traits\AttachPhotosTrait;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LegalEntityTest extends TestCase
{
    use DatabaseTransactions;
    use AttachPhotosTrait;
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

    public function testLegalEntityIndex()
    {
        $response = $this->get('api/legal-entities');

        $response->assertStatus(200);
    }

    // testing index pagination
    public function testLegalEntityIndexPagination()
    {
        $response = $this->get('api/legal-entities');
        
        $response->assertJsonCount(100, '0');

        $response->assertOk();
    }

    // testing show method
    public function testLegalEntityShow()
    {
        $legalEntity = LegalEntity::latest()->first();
        $response = $this->getJson('api/legal-entities/'.$legalEntity->id);

        $response->assertStatus(200);
    }

    // testing store method
    public function testLegalEntityStore()
    {  
        $credentials = [
            'name' => 'New Company',
            'director_full_name' => 'John Doe',
            'birth_date' => '20/04/1981',
            'country_id' => '2',
            'address' => 'John Street 3',
        ];
        
        $response = $this->postJson('api/legal-entities', $credentials);

        $response->assertStatus(201); // Assert that the response has a status code of 201 (Created)
        $this->assertDatabaseHas('legal_entities', ['director_full_name' => 'John Doe']); // Assert that the data is stored in the database
    
    }

    // testing update method
    // public function testLegalEntityUpdate()
    // {  
    //     $legalEntity = LegalEntity::factory()->create(
    //         [
    //             'name' => 'New Company',
    //             'director_full_name' => 'John Doe',
    //             'birth_date' => '20/04/1981',
    //             'country_id' => '2',
    //             'address' => 'John Street 3',
    //         ]
    //     );

    //     $newCredentials = [
    //         'name' => 'New Company and Co',
    //         'director_full_name' => 'Johny Doe',
    //         'birth_date' => '21/04/1981',
    //         'country_id' => '3',
    //         'address' => 'John Street 4',
    //     ];
        
    //     $response = $this->put('api/legal-entities/'.$legalEntity->id, $newCredentials);

    //     $response->assertStatus(200);

    //     $this->assertDatabaseHas('legal_entities', [
    //         'id' => $legalEntity->id,
    //         'name' => 'New Company and Co',
    //         'director_full_name' => 'Johny Doe',
    //         'birth_date' => '1981-04-21 00:00:00',
    //         'country_id' => '3',
    //         'address' => 'John Street 4',
    //     ]); // Assert that the data is stored in the database
    
    // }

    // // testing destroy method
    // public function testAddedUserDestroy()
    // {  
    //     $legalEntity = LegalEntity::factory()->create(
    //         [
    //             'name' => 'New Company',
    //             'director_full_name' => 'John Doe',
    //             'birth_date' => '20/04/1981',
    //             'country_id' => '2',
    //             'address' => 'John Street 3',
    //         ]
    //     );
        
    //     $response = $this->delete('api/legal-entities/'.$legalEntity->id);

    //     $response->assertStatus(204);

    //     $this->assertModelMissing($legalEntity); // Assert that the data is stored in the database
    
    // }
}
