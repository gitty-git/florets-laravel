<?php

namespace App\Http\Controllers;

use App\Models\Order;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($option)
    {
        // return $option;
        if ($option === 'all') {
             return Order::orderBy('created_at', 'DESC')->paginate(10);
        }
        else return Order::where('status', '=', $option)->orderBy('created_at', 'DESC')->paginate(10);
        
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $createdOrder = Order::create($validated);
        return $createdOrder;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order, $id)
    {
        return Order::where('id', $id)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $validated = $this->validateRequest($request);

        $order = Order::where('id', $order->id)
                ->update($validated);

        return $order;
    }

    private function validateRequest($request)
    {       
        return $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'delivery_time' => 'required',
            'cart' => 'required|json',
            'comment' => 'nullable|string',
            'status' => '',
            'paid' => ''
        ]);
    }
}
