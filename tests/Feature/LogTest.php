<?php

namespace Tests\Feature;

use App\Models\Log;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogTest extends TestCase
{
    use DatabaseTransactions;
    use HasFactory;

    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate a user
        $user = User::where('name','AdminTester')->first();
        $this->actingAs($user);
    }

    // testing index method
    public function testLogIndex()
    {
        $response = $this->get('api/logs');

        $response->assertStatus(200);
    }

    // testing index pagination
    public function testLogIndexPagination()
    {
        $response = $this->get('api/logs');
        
        $response->assertJsonCount(100, '0');

        $response->assertOk();
    }

    // testing index method with date filter
    // public function testLogIndexDateFilter()
    // {
    //     Log::factory()->times(5)->create([
    //         'date' => '2040-07-12 00:00:00',
    //     ]);
    //     // Log::factory(10)->create([
    //     //     'date' => '2/07/2023'
    //     // ]);
    //     // Log::factory(15)->create([
    //     //     'date' => '3/07/2023'
    //     // ]);

    //     $response = $this->get('api/logs?end_date=12/07/2040');

    //     $response->assertJsonCount(5);

    //     $response->assertStatus(200);
    // }
}
