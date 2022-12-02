<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function cancel($orderId)
    {
        $order = Order::where('id', $orderId)->firstOrFail();

        if ($order->paymentId) {
            
            // create token
            $str = env('TINKOFF_PASSWORD') . $order->paymentId . env('TINKOFF_TERMINAL');
            $token = hash("sha256", $str);

            $data = [
                "TerminalKey" => env('TINKOFF_TERMINAL'),
                "PaymentId" => $order->paymentId,
                "Token" => $token,
                // 'Receipt' => [
                //     'Phone' => $order->phone,
                //     'Taxation' => 'usn_income',
                //     'Items' => $order->items,
                // ]
            ];

            $url = env('TINKOFF_URL') . '/Cancel';
            $response = Http::withOptions(['verify' => false])->post($url, $data);

            if ($response->successful() && $response['Success']) return $response;
            else 
            {
                $returnData = [
                    'status' => 'Error',
                    'message' => 'Нет ответа от банка'
                ];
                
                return response()->json($returnData, 500);            
            }
        } 
        else {
            $returnData = [
                'status' => 'Ошибка',
                'message' => 'Платеж не найден'
            ];

            return response()->json($returnData, 404);
        }
    }

    public function getStatus($orderId) {
        $order = Order::where('id', $orderId)->firstOrFail();

        if ($order->paymentId) {
            $str = env('TINKOFF_PASSWORD') . $order->paymentId . env('TINKOFF_TERMINAL');
            $token = hash("sha256", $str);

            $data = [
                "TerminalKey" => env('TINKOFF_TERMINAL'),
                "PaymentId" => $order->paymentId,
                "Token" => $token,
            ];

            $url = env('TINKOFF_URL') . '/GetState';

            $response = Http::withOptions(['verify' => false])->post($url, $data);

            if ($response->successful()) {
                $returnData = [
                    'amount' => $response['Amount'] / 100,
                    'status' => $response['Status'],
                ];

                return response()->json($returnData, 200);
            } 
            else {
                $returnData = [
                    'status' => 'Error',
                    'message' => 'Нет ответа от банка'
                ];

                return response()->json($returnData, 200);
            }
        }
        else {
            $returnData = [
                'status' => 'NO_PAYMENT',
                'message' => 'Платеж не найден'
            ];

            return response()->json($returnData, 200);
        }
    }
}
