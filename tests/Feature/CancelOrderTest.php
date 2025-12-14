<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CancelOrderTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_cancel_sell_order_success(): void
    {
        $this->signIn();

        $asset = Asset::owned()->where('symbol', 'BTC')->first();

        // prepare order details
        $orderData = [
            'order_type' => 'sell',
            'symbol' => 'BTC',
            'amount' => 11.69,
            'price' => 267.13
        ];

        // make request
        $this->postJson('/api/orders', $orderData)->assertStatus(201);

        // verify data successfully recorded and accurate
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'amount' => $asset->amount - 11.69,
            'locked_amount' => 11.69,
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => auth()->id(),
            'symbol' => 'BTC',
            'side' => 'sell',
            'status' => 1,
            'amount' => 11.69,
            'price' => 267.13
        ]);

        $order = Order::owned()->first();

        // cancel order
        $this->postJson("/api/orders/{$order->id}/cancel")->assertStatus(200);

        // verify asset locked balance is released and order marked cancelled
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'amount' => $asset->amount,
            'locked_amount' => 0
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 3,

        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_cancel_buy_order_success(): void
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

        $order = Order::owned()->first();

        // cancel order
        $this->postJson("/api/orders/{$order->id}/cancel")->assertStatus(200);

        // verify user locked balance released and order marked cancelled
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'balance' => $userBalance,
            'locked_balance' => 0,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 3,
        ]);
    }
}
