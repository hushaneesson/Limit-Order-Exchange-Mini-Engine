<?php

namespace App\Processes;

use App\OrderTrait;
use App\Models\Order;
use App\Exceptions\InsufficientBalanceException;

class BuyOrderProcess
{
    use OrderTrait;

    public function __construct($orderData)
    {
        $orderTotal = round($orderData['amount'] * $orderData['price'], 2);

        $userBalance = $this->getUserBalance();

        // verify user has sufficient balance to make purchase
        if ($orderTotal > $userBalance) {
            throw new InsufficientBalanceException();
        }

        $this->lockOrderValue($orderTotal);

        $order = Order::create([
            'user_id' => auth()->id(),
            'side' => 'buy',
            'status' => 1,
            'symbol' => $orderData['symbol'],
            'price' => $orderData['price'],
            'amount' => $orderData['amount'],
        ]);

        return $order;
    }
}
