<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingsTest extends TestCase
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
    public function testSettingsIndex()
    {
        Setting::create([
            'limit'=>1000000,
            'usd_to_som'=>87,
            'usdt_to_som'=>87,
            'rub_to_som'=>1.5,
            'high_risk'=>100,
            'risk'=>1095,
        ]);

        $response = $this->get('api/settings');

        $response->assertStatus(200);
    }

    // testing store method
    public function testSettingsStore()
    {  
        $credentials = [
            'limit' => 1000,
            'usd_to_som' => 88,
            'usdt_to_som' => 88,
            'rub_to_som' => 1.05,
            'high_risk' => 365,
            'risk' => 1000,
        ];
        
        $response = $this->postJson('api/settings', $credentials);
        
        $this->assertDatabaseHas('settings', [
            'limit' => 1000,
            'usd_to_som' => 88,
            'usdt_to_som' => 88,
            'rub_to_som' => 1.05,
            'high_risk' => 365,
            'risk' => 1000,
        ]); // Assert that the data is stored in the database
    }

    // testing store method
    public function testIfSettingsStoreNotAddingButUpdating()
    {  
        $credentials = [
            'limit' => 1000,
            'usd_to_som' => 88,
            'usdt_to_som' => 88,
            'rub_to_som' => 1.05,
            'high_risk' => 365,
            'risk' => 1000,
        ];
        
        $response = $this->postJson('api/settings', $credentials);
       
        $this->assertDatabaseHas('settings', [
            'limit' => 1000,
            'usd_to_som' => 88,
            'usdt_to_som' => 88,
            'rub_to_som' => 1.05,
            'high_risk' => 365,
            'risk' => 1000,
        ]);

        $rowCount = Setting::count();

        $this->assertEquals(1, $rowCount);
    }
}
