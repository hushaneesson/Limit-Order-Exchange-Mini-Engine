<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewBuyOrderTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_buy_order_success(): void
    {
        $this->signIn();

        $userBalance = auth()->user()->balance;

        // prepare order details
        $orderData = [
            'order_type' => 'buy',
            'symbol' => 'BTC',
            'amount' => 20,
            'price' => 1.80
        ];

        // make request
        $this->postJson('/api/orders', $orderData)->assertStatus(201);

        // verify data successfully recorded and accurate
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'balance' => $userBalance - 36,
            'locked_balance' => 36,
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => auth()->id(),
            'symbol' => 'BTC',
            'side' => 'buy',
            'status' => 1,
            'price' => 1.80,
            'amount' => 20,
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_insufficient_balance(): void
    {
        $this->signIn();

        // prepare order details
        $orderData = [
            'order_type' => 'buy',
            'symbol' => 'BTC',
            'amount' => 1000,
            'price' => 180
        ];

        // make request
        $this->postJson('/api/orders', $orderData)
        ->assertStatus(400)
        ->assertJsonFragment(['Not enough balance for this purchase']);


    }
}
