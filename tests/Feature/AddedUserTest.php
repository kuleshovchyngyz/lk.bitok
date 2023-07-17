<?php

namespace Tests\Feature;

use App\Traits\AttachPhotosTrait;
use Tests\TestCase;
use App\Models\User;
use App\Models\AddedUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddedUserTest extends TestCase
{
    use DatabaseTransactions;
    use AttachPhotosTrait;
    
    public function testCheckDataInDatabase()
    {
        $roleId = Role::where('name', 'Admin')->value('id');
        $data = DB::table('model_has_roles')
            ->where('model_type', 'App\Models\User')
            ->where('model_id', 1)
            ->where('role_id', $roleId)
            ->get();

        // Perform assertions to check the data
        $this->assertCount(1, $data);
        $this->assertEquals($roleId, $data[0]->role_id);
    }

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

    /**
     * A basic feature test example.
     *
     * @return void
     */

    // testing index method
    public function testAddedUserIndex()
    {
        $response = $this->get('api/added-users');

        $response->assertStatus(200);
    }

    // testing index pagination
    public function testAddedUserIndexPagination()
    {
        $response = $this->get('api/added-users');
        
        $response->assertJsonCount(100, '0');

        $response->assertOk();
    }

    // testing show method
    public function testAddedUserShow()
    {
        $addedUser = AddedUser::latest()->first();
        $response = $this->getJson('api/added-users/'.$addedUser->id);

        $response->assertStatus(200);
    }

    // testing store method
    public function testAddedUserStore()
    {  
        $credentials = [
            'last_name' => 'Doe',
            'first_name' => 'John',
            'middle_name' => 'Junior',
            'birth_date' => '20/04/1981',
            'country_id' => '2',
            'pass_num_inn' => '21409200040935',
            'passport_id' => '100000000',
            'passport_authority' => 'Minstry Affairs',
            'passport_authority_code' => '21309200000935',
            'passport_issued_at' => '20/04/2005',
            'passport_expires_at' => '20/04/2015',
            'verification_date' => '20/04/2015',
            'verification' => '1',
        ];
        
        $response = $this->postJson('api/added-users', $credentials);

        $response->assertStatus(201); // Assert that the response has a status code of 201 (Created)
        $this->assertDatabaseHas('added_users', ['last_name' => 'Doe']); // Assert that the data is stored in the database
    
    }

    // testing update method
    public function testAddedUserUpdate()
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

        $newCredentials = [
            'last_name' => 'Doer',
            'first_name' => 'Johny',
            'middle_name' => 'Senior',
            'birth_date' => '20/04/1991',
            'registration_date' => '25/12/2022 18:14',
            'country_id' => '3',
            'pass_num_inn' => '21409200040935',
            'passport_id' => '100000000',
            'passport_authority' => 'Ministry Affairs',
            'passport_authority_code' => '21309200000935',
            'passport_issued_at' => '20/04/2005',
            'passport_expires_at' => '20/04/2015',
            'verification_date' => '20/04/2015',
            'verification' => '0',
        ];
        
        $response = $this->put('api/added-users/'.$addedUser->id, $newCredentials);

        $response->assertStatus(200);
        $this->assertDatabaseHas('added_users', [
            'id' => $addedUser->id,
            'last_name' => 'Doer',
            'first_name' => 'Johny',
            'middle_name' => 'Senior',
            'birth_date' => '1991-04-20 00:00:00',
            'country_id' => '3',
            'pass_num_inn' => '21409200040935',
            'passport_id' => '100000000',
            'passport_authority' => 'Ministry Affairs',
            'passport_authority_code' => '21309200000935',
            'passport_issued_at' => '20/04/2005',
            'passport_expires_at' => '20/04/2015',
            'verification_date' => '2015-04-20 00:00:00',
            'verification' => '0',
        ]); // Assert that the data is stored in the database
    
    }

    // testing destroy method
    public function testAddedUserDestroy()
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
        
        $response = $this->delete('api/added-users/'.$addedUser->id);

        $response->assertStatus(204);

        $this->assertModelMissing($addedUser); // Assert that the data is stored in the database
    
    }

    // public function testUploadMethod()
    // {
    //     Storage::fake('public');

    //     $addedUser = AddedUser::factory()->create([
    //         'last_name' => 'Doe',
    //         'first_name' => 'John',
    //         'middle_name' => 'Junior',
    //         'birth_date' => '20/04/1981',
    //         'country_id' => '2',
    //         'pass_num_inn' => '21409200040835',
    //         'passport_id' => '100000000',
    //         'passport_authority' => 'Minstry Affairs',
    //         'passport_authority_code' => '21309200000935',
    //         'passport_issued_at' => '20/04/2005',
    //         'passport_expires_at' => '20/04/2015',
    //         'verification_date' => '20/04/2015',
    //         'verification' => '1',
    //     ]);

    //     $file = UploadedFile::fake()->image('passport_photo.jpg');

    //     $this->attach([$file], $addedUser, 'passport');

    //     $response = $this->postJson('api/added-users/'.$addedUser->id.'/upload', [
    //         'passport_photo' => [$file],
    //     ]);

    //     $response->assertStatus(200);

    //     $addedUser->refresh();

    // }
}
