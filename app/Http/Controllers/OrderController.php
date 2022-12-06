<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Size;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    private $discount = 0;

    private function countDisc($num)
    {
        return $num - $num * $this->discount;
    }

    public function index($option)
    {
        if ($option === 'all') {
             return Order::orderBy('created_at', 'DESC')->paginate(10);
        }
        else return Order::where('status', '=', $option)->orderBy('created_at', 'DESC')->paginate(10);          
    }

    public function getStatus($id)
    {
        return Order::where('id', $id)->select(['status', 'id'])->firstOrFail();
    }

    public function store(Request $request)
    {
        $cart = json_decode($request->cart);        

        // discount
        if ($request->delivery_method === 'self_delivery') {
            $this->discount = 0.1;
        }

        $amount = 0;
        // get items
        $items = [];
        foreach ($cart as $key => $val) {            
            $res = Size::where('id', $val->id)->with('product')->firstOrFail();
            $amount += $this->countDisc($res->price * $val->quantity);
            if ($res->id === $val->id) {
                $items[$key] = [
                    'Name' => $res->product->name . ' - ' . strtoupper($res->name),
                    'Quantity' => $val->quantity,
                    'Amount' => $this->countDisc($res->price * $val->quantity * 100),
                    'Price' => $this->countDisc($res->price * 100),
                    'Tax' => 'none'
                ];
            }
        }

        $validated = $this->validateRequest($request, true);        
        $createdOrder = Order::create($validated);
        $admins = User::where('role', 'admin')->orWhere('role', 'employee')->get();
        Notification::send($admins, new NewOrderNotification($createdOrder, $items));

        // все ли размеры продуктов существуют?
        $equal = count($cart) === count($items);
        if (!$equal) return ['status' => 404, 'Some products not found'];

        // set data
        if ($request->payment_method === 'online') {      

            $data = [
                'TerminalKey' => env('TINKOFF_TERMINAL'),
                'Amount' => $amount * 100,
                'OrderId' => $createdOrder->id,
                'SuccessURL' => env('SUCCESS_URL') . "/$createdOrder->id",
                'FailURL' => env('FAIL_URL'),
                
                'DATA' => [
                    'Phone' => $createdOrder->phone,
                    'Name' => $createdOrder->name,
                ],
                'Receipt' => [
                    'Phone' => $createdOrder->phone,
                    'Email' => $createdOrder->email,
                    'Taxation' => 'usn_income',
                    'Items' => $items,
                ]
            ];            

            $url = env('TINKOFF_URL') . '/Init';
            $response = Http::withOptions(['verify' => false])->post($url, $data);

            if ($response->successful() && $response['Success'] === true) {
                Order::where('id', $createdOrder->id)->update([
                    'PaymentId' => $response['PaymentId'],
                    'items' => json_encode($items)
                ]);

                return $response;
            }
            else {
                Order::where('id', $createdOrder->id)->delete();
                $returnData = [
                    'status' => 'Error',
                    'message' => 'Не удалось создать заказ'
                ];
                return response()->json($returnData, 500);
            }
        }
        
        return $createdOrder;
    }

    public function show(Order $order, $id)
    {
        $order = Order::where('id', $id)->firstOrFail();

        $amount = 0;
        foreach (json_decode($order->cart) as $key => $val) {
            $res = Size::where('id', $val->id)->with('product')->first();
            if ($res) {
                $amount += $this->countDisc($res->price * $val->quantity);
            }            
        }

        if ($order->delivery_method === 'self_delivery') {
            $this->discount = 0.1;
        }

        $order->amount = $this->countDisc($amount);
        $order->makeHidden(['paymentId']);            
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
            "email" => "$isRequired|string",
            "address" => "$isRequired|string",
            "payment_method" => "$isRequired|string",
            "delivery_time" => "$isRequired",
            "cart" => "$isRequired|json",
            "comment" => "nullable|string",
            "apt" => "nullable|string",
            "status" => "nullable|string",
            "paid" => "nullable",
            "delivery_method" => "$isRequired|string",
            "receiver_name" => "required_if:delivery_method,another",
            "receiver_phone" => "required_if:delivery_method,another"
        ]);
    }
}
