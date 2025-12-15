<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Asset;
use App\Models\Order;
use App\Jobs\MatchOpenOrdersJob;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MatchOrderTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_match_open_orders_success(): void
    {
        $sellerBalance = $this->createSellOrder();

        $initialAssetAmount = $this->createBuyOrder();

        $sellOrder = Order::where('side', 'sell')->first();
        $buyOrder = Order::where('side', 'buy')->first();

        // execute match order job
        $this->artisan('app:match-open-orders');

        // verify orders were matched successfully
        $this->assertDatabaseHas('orders', [
            'id' => $sellOrder->id,
            'status' => 2,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $buyOrder->id,
            'status' => 2,
        ]);

        $this->assertDatabaseHas('trades', [
            'buy_order_id' => $buyOrder->id,
            'sell_order_id' => $sellOrder->id,
            'commission' => 0.54,
        ]);

        // verify seller locked asset amount decreased and sale balance applied
        $this->assertDatabaseHas('users', [
            'id' => $sellOrder->user_id,
            'balance' => $sellerBalance + (36 - 0.54),
        ]);

        $this->assertDatabaseHas('assets', [
            'user_id' => $sellOrder->user_id,
            'symbol' => $sellOrder->symbol,
            'locked_amount' => 0
        ]);


        // verify buyer asset amount increased and sale balance transferred
        $this->assertDatabaseHas('users', [
            'id' => $buyOrder->user_id,
            'locked_balance' => 0,
        ]);

        $asset = Asset::where([
            'user_id' => $buyOrder->user_id,
            'symbol' => $buyOrder->symbol,
        ])->first();

        $this->assertEquals(
            $asset->amount,
            $initialAssetAmount + 20
        );
    }

    public function createSellOrder()
    {
        $user = User::where('email', '!=', 'test@example.com')->first();

        $this->signIn($user);

        $asset = $user->assets()->where('symbol', 'BTC')->first();

        // prepare order details
        $orderData = [
            'order_type' => 'sell',
            'symbol' => 'BTC',
            'amount' => 20,
            'price' => 1.8
        ];

        // make request
        $this->postJson('/api/orders', $orderData)->assertStatus(201);

        // verify data successfully recorded and accurate
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'amount' => $asset->amount - 20,
            'locked_amount' => 20,
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'symbol' => 'BTC',
            'side' => 'sell',
            'status' => 1,
            'amount' => 20,
            'price' => 1.8
        ]);

        return $user->balance;
    }

    public function createBuyOrder()
    {
        $this->signIn();

        $userBalance = auth()->user()->balance;

        $asset = auth()->user()->assets()->where('symbol', 'BTC')->first();

        // prepare order details
        $orderData = [
            'order_type' => 'buy',
            'symbol' => 'BTC',
            'amount' => 20,
            'price' => 1.8
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

        return $asset->amount;
    }
}
