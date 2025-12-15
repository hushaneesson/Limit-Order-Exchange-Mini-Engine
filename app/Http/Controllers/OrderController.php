<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Processes\BuyOrderProcess;
use Illuminate\Support\Facades\DB;
use App\Processes\SellOrderProcess;
use Illuminate\Support\Facades\Log;
use App\Processes\CancelOrderProcess;
use App\Http\Resources\OrderCollection;
use App\Http\Requests\LimitOrderRequest;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InsufficientBalanceException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::owned()->latest();

        if ($request->symbol) {
            $query->where('symbol', $request->symbol);
        }

        if ($request->side) {
            $query->where('side', $request->side);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        return new OrderCollection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LimitOrderRequest $orderData)
    {
        try {
            DB::beginTransaction();

            switch ($orderData->order_type) {
                case 'buy':
                    new BuyOrderProcess($orderData);
                    break;

                case 'sell':
                    new SellOrderProcess($orderData);
                    break;

                default:
                    throw new \Exception('Invalid order action');
            }

            DB::commit();

            return response()->json(['order submitted successfully'], 201);
        } catch (InsufficientBalanceException $e) {
            DB::rollback();

            return response()->json(['Not enough balance for this purchase'], 400);
        } catch (InsufficientStockException $e) {
            DB::rollback();

            return response()->json(['Not enough stock for this sale'], 400);
        } catch (\Exception $e) {
            DB::rollback();

            Log::debug($e->getMessage());
            Log::debug($e->getTraceAsString());

            return response()->json(['Error submitting order'], 400);
        }
    }


    /**
     * Mark order as cancelled
     */
    public function cancel($id)
    {
        $order = Order::lockForUpdate()->owned()->findOrFail($id);

        try {
            DB::beginTransaction();

            // only open orders can be cancelled
            if ($order->status != 1) {
                return response()->json(['Only open orders can be cancelled'], 400);
            }

            new CancelOrderProcess($order);

            DB::commit();

            return response()->json(['Order successfully cancelled.']);
        } catch (\Exception $e) {
            DB::rollback();

            Log::debug($e->getMessage());
            Log::debug($e->getTraceAsString());

            return response()->json(['Error cancelling order'], 400);
        }
    }
}
