<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class OrderController extends Controller
{

    public function index($option)
    {
        // return $option;
        if ($option === 'all') {
             return Order::orderBy('created_at', 'DESC')->paginate(10);
        }
        else return Order::where('status', '=', $option)->orderBy('created_at', 'DESC')->paginate(10);          
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request, true);
        $createdOrder = Order::create($validated);
        $cart = json_decode($createdOrder->cart);

        $amount = 0;
        for ($i = 0; $i < count($cart); $i++) {
            $amount += $cart[$i]->amount * $cart[$i]->price;
        }

        if ($createdOrder->way_to_receive === 'self_delivery') {
            $amount = $amount - $amount * 0.1;
        }

        if ($request->payment_method === 'online') {           
            $data = [
                'TerminalKey' => env('TINKOFF_TERMINAL'),
                'Amount' => $amount * 100,
                'OrderId' => $createdOrder->id,
                'Description' => 'tinkoffpay',
                'NotificationURL' => env('NOTIFICATION_URL'),
                'SuccessURL' => env('SUCCESS_URL') . "/$createdOrder->id/paid",
                'FailURL' => env('FAIL_URL'),
                'DATA' => [
                    'Phone' => $request->phone,
                    'Name' => $request->name,
                ],
                // 'Receipt' => [
                //     'Phone' => $phone,
                //     'Items' => [
                //         'Name' => 'Bouq',
                //         'Price' => 10000,
                //         'Quantity' => 1.00,
                //         'Amount' => 10000,
                //     ]
                // ]
            ];

            $url = env('TINKOFF_URL') . '/Init';
            $response = Http::withOptions(['verify' => false])->post($url, $data);            
            Order::where('id', $createdOrder->id)->update(['PaymentId' => $response['PaymentId'], 'amount' => $amount * 100]);
            
            return $response;
        }
        
        return $createdOrder;
    }

    public function show(Order $order, $id)
    {
        return $order = Order::where('id', $id)->firstOrFail()->makeHidden(['paymentId']);

        $arr = [
            'Amount' => $order->amount,
            'TerminalKey' => env('TINKOFF_TERMINAL'),
            'Password' => env('TINKOFF_PASSWORD'),
        ];

        ksort($arr);

        $str = '';

        foreach ($arr as $key => $val) {
            $str .= $val;
        }

        $str = '1664975277721DEMO' . 'jv7qy20l6etupd08';
        
        $token = hash("sha256", $str);

        $data = [
            "TerminalKey" => env('TINKOFF_TERMINAL'),
            "OrderId" => $order->id,
            "Token" => $token,
        ];

        $url = env('TINKOFF_URL') . '/CheckOrder';

        $response = Http::withOptions(['verify' => false])->post($url, $data);
        return $response;
    }

    public function update(Request $request, Order $order)
    {
        $validated = $this->validateRequest($request, false);

        $order = Order::where('id', $order->id)
                ->update($validated);
        return $order;
    }

    private function validateRequest($request, $required)
    {
        if ($required) $isRequired = 'required';
        else $isRequired = 'nullable';

        return $request->validate([
            'viewed' => "nullable",
            'name' => "$isRequired|string",
            "phone" => "$isRequired|string",
            "address" => "$isRequired|string",
            "payment_method" => "$isRequired|string",
            "delivery_time" => "$isRequired",
            "cart" => "$isRequired|json",
            "comment" => "nullable|string",
            "status" => "nullable|string",
            "paid" => "nullable",
            "way_to_receive" => "$isRequired|string",
            "receiver_name" => "required_if:way_to_receive,another",
            "receiver_phone" => "required_if:way_to_receive,another"
        ]);
    }
}
