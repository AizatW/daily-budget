<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseAccessTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_expenses(): void
    {
        $response = $this->get('/expenses');

        $response->assertRedirect('/login');
    }

    public function test_uses_sqlite_testing_database(): void
    {
        $connection = config('databse.default');
        $database = config('database.connections.sqlite.database');

        $this->assertSame('sqlite, $connection');
        $this->assertSame(':memory:', $database);
    }
}
