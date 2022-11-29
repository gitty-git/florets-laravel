<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class OrderController extends Controller
{
    private $deliveryDiscount = 0;

    private function countDisc($num)
    {
        return $num - $num * $this->deliveryDiscount;
    }

    public function index($option)
    {
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

        $items = [];
        $deliveryDiscount = 0;

        if ($createdOrder->way_to_receive === 'self_delivery') {
            $this->deliveryDiscount = 0.1;
        }

        $amount = 0;
        foreach ($cart as $key => $val) {            
            $res = Attribute::where('id', $val->id)->with('product')->firstOrFail();
            $amount += $this->countDisc($res->price * $val->amount);
            if ($res->id === $val->id) {
                $items[$key] = [
                    'Name' => $res->product->name . ' - ' . strtoupper($res->size),
                    'Quantity' => $val->amount,
                    'Amount' => $this->countDisc($res->price * $val->amount * 100),
                    'Price' => $this->countDisc($res->price * 100),
                    'Tax' => 'none'
                ];
            }
        }

        // все ли размеры продуктов существуют?
        $equal = count($cart) + 1 === count($items);
        if (!$equal) return ['status' => 404, 'Some products not found'];        

        if ($request->payment_method === 'online') {           
            $data = [
                'TerminalKey' => env('TINKOFF_TERMINAL'),
                'Amount' => $amount * 100,
                'OrderId' => $createdOrder->id,
                'NotificationURL' => env('NOTIFICATION_URL'),
                'SuccessURL' => env('SUCCESS_URL') . "/$createdOrder->id/paid",
                'FailURL' => env('FAIL_URL'),
                
                'DATA' => [
                    'Phone' => $createdOrder->phone,
                    'Name' => $createdOrder->name,
                ],
                'Receipt' => [
                    'Phone' => $createdOrder->phone,
                    'Taxation' => 'usn_income',
                    'Items' => $items,
                ]
            ];

            $url = env('TINKOFF_URL') . '/Init';
            $response = Http::withOptions(['verify' => false])->post($url, $data);
            // return $response;
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
