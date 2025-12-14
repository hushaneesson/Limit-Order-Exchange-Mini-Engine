<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Processes\BuyOrderProcess;
use Illuminate\Support\Facades\DB;
use App\Processes\SellOrderProcess;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OrderResource;
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
        $filter = [
            'page' => $request->page ?? 1,
            'size' => $request->size ?? 10
        ];

        $query = Order::owned()->latest();

        if ($request->symbol) {
            $query->where('symbol', $request->symbol);
        }

        $orders = $query->paginate($filter['size'], '*', 'page', $filter['page']);

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
     * Remove the specified resource from storage.
     */
    public function cancel(Order $order)
    {
        //
    }
}
