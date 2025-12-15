<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Asset;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewSellOrderTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_sell_order_success(): void
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
    }

    /**
     * A basic feature test example.
     */
    public function test_insufficient_stock(): void
    {
        $this->signIn();

        // prepare order details
        $orderData = [
            'order_type' => 'sell',
            'symbol' => 'BTC',
            'amount' => 151.6,
            'price' => 2670.13
        ];

        // make request
        $this->postJson('/api/orders', $orderData)
            ->assertStatus(400)
            ->assertJsonFragment(['Not enough stock for this sale']);
    }
}
