<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use App\Jobs\MatchOpenOrdersjob;

class MatchOpenOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:match-open-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description loads open orders and tries to match them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = 30;

        Order::where('status', 1) // open
            ->orderBy('created_at', 'asc')
            ->select('id')
            ->chunkById($chunkSize, function ($orders) {
                foreach ($orders as $order) {
                    MatchOpenOrdersJob::dispatch($order->id);
                }
            });

        return self::SUCCESS;
    }
}
