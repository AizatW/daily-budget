<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;

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
        $connection = config('database.default');
        $database = config('database.connections.sqlite.database');

        $this->assertSame('sqlite', $connection);
        $this->assertSame(':memory:', $database);
    }

    public function test_authenticated_user_can_access_expenses(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/expenses');

        $response->assertStatus(200);
    }

    public function test_users_can_only_see_their_own_expenses(): void 
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $userA->expenses()->create([
            'input_date' => now()->toDateString(),
            'descriptions' => 'User A private expense',
            'amount' => 50,
            'type' => 'expense',
            'payment_type' => 'cash',
        ]);
        $userB->expenses()->create([
            'input_date' => now()->toDateString(),
            'descriptions' => 'User B private expense',
            'amount' => 150,
            'type' => 'expense',
            'payment_type' => 'online',
        ]);


        $this->assertDatabaseHas('expenses', [
            'user_id' => $userA->id,
            'descriptions' => 'User A private expense',
        ]);

        $response = $this->actingAs($userA)->get('/expenses?month=' . now()->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee('User A private expense');
        $response->assertDontSee('User B private expense');
    }

     public function test_user_cannot_delete_another_users_expense(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $expense = $userA->expenses()->create([
            'input_date' => now()->toDateString(),
            'descriptions' => 'User A protected expense',
            'amount' => 200,
            'type' => 'expense',
            'payment_type' => 'online'
        ]);

        $response = $this->actingAs($userB)
            ->delete('/expenses/' . $expense->id);

        $response->assertStatus(404);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'user_id' => $userA->id,
            'descriptions' => 'User A protected expense'
        ]);
    }

    public function test_user_cannot_update_another_users_expense(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $expense = $userA->expenses()->create([
            'input_date' => now()->toDateString(),
            'descriptions' => 'User A protected expense',
            'amount' => 200,
            'type' => 'expense',
            'payment_type' => 'cash',
        ]);

        $response = $this->actingAs($userB)
            ->put('/expenses/' . $expense->id, [
                'input_date' => now()->toDateString(),
                'descriptions' => 'Modified by User B',
                'amount' => 2000,
                'type' => 'expense',
                'payment_type' => 'online',
            ]);

        $response->assertStatus(404);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'user_id' => $userA->id,
            'descriptions' => 'User A protected expense',
            'amount' => 200,
            'payment_type' => 'cash',
        ]);
    }

    public function test_user_cannot_create_expense_with_negative_amount(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/expenses', [
                'input_date' => now()->toDateString(),
                'descriptions' => 'Invalid expense',
                'amount' => -50,
                'type' => 'expense',
                'payment_type' => 'cash',
            ]);

        $response->assertSessionHasErrors('amount');

        $this->assertDatabaseMissing('expenses', [
            'user_id' => $user->id,
            'descriptions' => 'Invalid expense',
        ]);
    }
}
