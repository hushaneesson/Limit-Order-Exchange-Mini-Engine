<?php

namespace App\Processes;

use App\OrderTrait;
use App\Models\Order;
use App\Exceptions\InsufficientStockException;

class SellOrderProcess
{
    use OrderTrait;

    public function __construct($orderData)
    {
        $asset = $this->getUserAsset($orderData['symbol']);

        // verify user has sufficient stock to make sale
        if ($asset->amount < $orderData['amount']) {
            throw new InsufficientStockException();
        }

        // lock sale amount
        $asset->amount -= $orderData['amount'];
        $asset->locked_amount += $orderData['amount'];
        $asset->save();

        $order = Order::create([
            'user_id' => auth()->id(),
            'side' => 'sell',
            'status' => 1,
            'symbol' => $orderData['symbol'],
            'price' => $orderData['price'],
            'amount' => $orderData['amount'],
        ]);

        return $order;
    }
}
