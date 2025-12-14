<?php

namespace App;

use App\Models\Asset;

trait OrderTrait
{
    public function getUserBalance()
    {
        return auth()->user()->balance;
    }

    public function lockOrderValue(float $amount)
    {
        $amount = round($amount, 2);

        $this->reduceUserBalance($amount);

        auth()->user()->increment('locked_balance', $amount);
    }

    public function reduceUserBalance(float $amount)
    {
        $amount = round($amount, 2);

        auth()->user()->decrement('balance', $amount);
    }

    public function getUserAsset($symbol)
    {
        return Asset::owned()
            ->where('symbol', $symbol)
            ->first();
    }
}
