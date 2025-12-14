<?php

namespace App;

use App\Models\Asset;

trait OrderTrait
{
    public function getUserBalance()
    {
        return auth()->user()->balance;
    }

    public function getUserAsset($symbol)
    {
        return Asset::owned()
            ->where('symbol', $symbol)
            ->first();
    }

    public function lockOrderValue(float $amount)
    {
        $amount = round($amount, 2);

        auth()->user()->decrement('balance', $amount);
        auth()->user()->increment('locked_balance', $amount);
    }


    public function releaseLockedOrderValue(float $amount)
    {
        $amount = round($amount, 2);

        auth()->user()->increment('balance', $amount);
        auth()->user()->decrement('locked_balance', $amount);
    }
}
