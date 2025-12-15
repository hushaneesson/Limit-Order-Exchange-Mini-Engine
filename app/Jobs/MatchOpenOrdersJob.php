<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Order;
use App\Models\Trade;
use App\Events\OrderMatched;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use App\Exceptions\NoMatchedOrderException;
use Illuminate\Contracts\Queue\ShouldQueue;

class MatchOpenOrdersJob implements ShouldQueue
{
    use Queueable;

    public $orderId;
    const COMMISSION = 0.015;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $order = Order::lockForUpdate()->find($this->orderId);

            // only open orders should be processed
            if (!$order || $order->status != 1) {
                return;
            }

            switch ($order->side) {
                case 'buy':
                    $buyOrder = $order;
                    $sellOrder = $this->matchBuyOrder($order);
                    break;

                case 'sell':
                    $buyOrder = $this->matchSellOrder($order);
                    $sellOrder = $order;
                    break;
            }

            // record trade and calculate commission
            $trade = $this->recordTrade($sellOrder, $buyOrder);

            // increase buyer asset amount
            $buyOrder->asset()->increment('amount', $sellOrder->amount);

            // reduce buyer locked balance
            $buyOrder->user()->decrement('locked_balance', $trade->trade_value);

            // reduce seller locked asset amount
            $sellOrder->asset()->decrement('locked_amount', $sellOrder->amount);

            // increase seller balance less commission
            $sellOrder->user()->increment('balance', $trade->trade_value - $trade->commission);

            // mark orders as filled
            $sellOrder->update(['status' => 2]);
            $buyOrder->update(['status' => 2]);

            // fetch seller updated details
            $sellerData = $this->fetchUserProfile($sellOrder->user_id);
            $sellerData['updated_order_id'] = $sellOrder->id;

            // fetch buyer updated details
            $buyerData = $this->fetchUserProfile($buyOrder->user_id);
            $buyerData['updated_order_id'] = $buyOrder->id;

            DB::commit();

            // notify users about trade
            event(new OrderMatched(
                $buyOrder->user_id,
                $sellOrder->user_id,
                $buyerData,
                $sellerData
            ));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    protected function matchBuyOrder($buyOrder)
    {
        $matchedOrder = Order::where('side', 'sell')
            ->where('symbol', $buyOrder->symbol)
            ->where('price', '<=', $buyOrder->price)
            ->where('amount', $buyOrder->amount)
            ->lockForUpdate()
            ->first();

        if (!$matchedOrder) {
            throw new NoMatchedOrderException();
        }

        return $matchedOrder;
    }

    protected function matchSellOrder($sellOrder)
    {
        $matchedOrder = Order::where('side', 'buy')
            ->where('symbol', $sellOrder->symbol)
            ->where('price', '>=', $sellOrder->price)
            ->where('amount', $sellOrder->amount)
            ->lockForUpdate()
            ->first();

        if (!$matchedOrder) {
            throw new NoMatchedOrderException();
        }

        return $matchedOrder;
    }

    protected function recordTrade($sellOrder, $buyOrder)
    {
        $orderTotal = round($sellOrder->price * $sellOrder->amount, 2);
        $commission = round($orderTotal * self::COMMISSION, 2);

        $trade = Trade::create([
            'buy_order_id' => $buyOrder->id,
            'sell_order_id' => $sellOrder->id,
            'trade_value' => $orderTotal,
            'commission' => $commission,
        ]);

        return $trade;
    }

    public function fetchUserProfile($userId)
    {
        $user = User::find($userId);

        $profile = [
            'balance' => $user->balance,
            'assets' => $user->assets->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'symbol' => $asset->symbol,
                    'amount' => $asset->amount,
                    'locked_amount' => $asset->locked_amount,
                ];
            })
        ];

        return $profile;
    }
}
