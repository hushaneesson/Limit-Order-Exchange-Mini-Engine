<?php

namespace App\Processes;

use App\OrderTrait;
use App\Models\Order;

class CancelOrderProcess
{
    use OrderTrait;

    public function __construct(Order $order)
    {
        if ($order->side == 'buy') {
            $orderTotal = round($order->amount * $order->price, 2);

            $this->releaseLockedOrderValue($orderTotal);
        }


        if ($order->side == 'sell') {
            $asset = $order->asset;

            $asset->amount += $order->amount;
            $asset->locked_amount -= $order->amount;
            $asset->save();
        }

        $order->update(['status' => 3]);
    }
}
