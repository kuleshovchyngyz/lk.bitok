<?php

namespace Tests\Feature;

use App\Models\Country;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CountryTest extends TestCase
{
    use DatabaseTransactions;
    
    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate a user
        $user = User::where('name','AdminTester')->first();
        $this->actingAs($user);
    }
    
    // testing index method
    public function testCountryIndex()
    {
        $response = $this->get('api/countries');

        $response->assertStatus(200);
    }

    // testing show method
    public function testCountryShow()
    {
        $country = Country::latest()->first();
        $response = $this->getJson('api/countries/'.$country->id);

        $response->assertStatus(200);
    }

    // testing store method
    public function testCountryStore()
    {  
        $credentials = [
            'name' => 'Newcountria',
            'sanction' => '1',
        ];
        
        $response = $this->postJson('api/countries', $credentials);

        $response->assertStatus(201); // Assert that the response has a status code of 201 (Created)
        $this->assertDatabaseHas('countries', ['name' => 'Newcountria']); // Assert that the data is stored in the database
    
    }

    // testing bulkUpdate method
    public function testCountryBulkUpdate()
    {  
        $now = Carbon::now();
        $newCountries = [
            ["name" => "Afghanistan", "sanction" => "0", "created_at" => $now],
            ["name" => "Åland Islands", "sanction" => "0", "created_at" => $now],
            ["name" => "Albania", "sanction" => "0", "created_at" => $now],
            ["name" => "Algeria", "sanction" => "0", "created_at" => $now],
            ["name" => "American Samoa", "sanction" => "0", "created_at" => $now],
        ];
        Country::insert($newCountries);
        
        $countries = Country::latest()->take(5)->get();

        $updatedCountries = [];

        for ($i=0; $i < count($countries); $i++) { 
            $updatedCountries[] = ['id' => $countries[$i]->id,'sanction' => '1'];
        }

        $response = $this->postJson('api/countries/bulk', $updatedCountries);

        $response->assertStatus(200);
        $this->assertDatabaseHas('countries', [
            'name' => 'Afghanistan', 'sanction' => '1',
            'name' => 'Åland Islands', 'sanction' => '1',
            'name' => 'Albania', 'sanction' => '1',
            'name' => 'Algeria', 'sanction' => '1',
            'name' => 'American Samoa', 'sanction' => '1',
        ]);
    
    }
}
