<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke()
    {
        $data = [
            'balance' => auth()->user()->balance,
            'assets' => []
        ];

        $data['assets'] = Asset::where('user_id', auth()->id())->select('id', 'symbol', 'amount', 'locked_amount')->get();

        return response()->json($data);
    }
}
