<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Size;
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
        $cart = json_decode($request->cart);

        $items = [];

        if ($request->delivery_method === 'self_delivery') {
            $this->deliveryDiscount = 0.1;
        }

        $amount = 0;
        foreach ($cart as $key => $val) {            
            $res = Size::where('id', $val->id)->with('product')->firstOrFail();
            $amount += $this->countDisc($res->price * $val->quantity);
            if ($res->id === $val->id) {
                $items[$key] = [
                    'Name' => $res->product->name . ' - ' . strtoupper($res->size),
                    'Quantity' => $val->quantity,
                    'Amount' => $this->countDisc($res->price * $val->quantity * 100),
                    'Price' => $this->countDisc($res->price * 100),
                    'Tax' => 'none'
                ];
            }
        }

        $validated = $this->validateRequest($request, true);        
        $createdOrder = Order::create($validated);

        // все ли размеры продуктов существуют?
        $equal = count($cart) === count($items);
        if (!$equal) return ['status' => 404, 'Some products not found'];

        if ($request->payment_method === 'online') {           
            $data = [
                'TerminalKey' => env('TINKOFF_TERMINAL'),
                'Amount' => $amount * 100,
                'OrderId' => $createdOrder->id,
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
            Order::where('id', $createdOrder->id)->update(['PaymentId' => $response['PaymentId']]);
            
            return $response;
        }
        
        return $createdOrder;
    }

    public function show(Order $order, $id)
    {
        $order = Order::where('id', $id)->firstOrFail();

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

            if ($response && $response['Success'] === true) {
                $order->amount = $response['Amount'] / 100;
                $order->online_payment_status = 'CONFIRMED';
            }
            else $order->online_payment_status = 'BANK_RESPONSE_ERR';

            $order->makeHidden(['paymentId']);
            
            return $order;
        }
        $order->online_payment_status = 'NO_PAYMENT';
        return $order;

    }

    public function update(Request $request, $id)
    {
        $request->online_payment_status = '';
        $request->amount = '';
        $validated = $this->validateRequest($request, false);    

        $order = Order::where('id', $id)
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
            "delivery_method" => "$isRequired|string",
            "receiver_name" => "required_if:delivery_method,another",
            "receiver_phone" => "required_if:delivery_method,another"
        ]);
    }
}
