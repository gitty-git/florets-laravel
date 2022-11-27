<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function handle(Request $request)
    {        
        if ($request->Success === true) {
            Order::where('id', $request->paymentId)->update(['paid' => 1]);
        }
        else Order::where('id', $request->paymentId)->update(['status' => 'canceled']);
        // Log::info('Tinkoff', [$request]);
    }
}
