<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getPaginatedOrders()
    {
        $orders = Order::with('user.profile', 'location', 'order_details')->paginate(10);
        return response()->json($orders, 200);
    }

    public function addOrderDetail(Request $request)
    {
        $order = Order::where("order_number", $request['order_number'])->firstOrFail();
        $orderDetailArr = array(
            "sku" => $request['sku'],
            "item_name" => $request['item_name'],
            "non_foc_quantity" => $request['non_foc_quantity'],
            "foc_quantity" => $request['foc_quantity'],
            "total_quantity" => $request['total_quantity'],
            "price" => $request['price'],
            "line_price" => $request['line_price'],
        );
        // $order->order_details()->updateOrCreate(["order_id" => $request['order_id']], $orderDetailArr);
        $order->order_details()->create($orderDetailArr);
        return response()->json($order, 200);
    }

    public function getSingleOrder($ordernum)
    {
        $order = Order::where("order_number", $ordernum)->with('order_details', 'location')->first();
        return response()->json($order, 200);
    }

    public function updateOrder(Request $request)
    {
        $orderArr = array(
            'status' => $request['status'],
            'location_id' => $request['location_id'],
            'user_id' => auth()->id()
        );
        $order = Order::where('order_number', $request['order_number'])->update($orderArr);


        return response()->json([
            'message' => "Order has been updated"
        ], 200);
    }

    public function createOrder(Request $request)
    {

        $order = Order::create([
            'status' => 'draft',
            'order_number' => auth()->id()."-".date('Y-mdHis'),
            'user_id' => auth()->id()
        ]);
        return response()->json($order, 200);
    }

    public function saveOrder(Request $request)
    {
        $msg = isset($request['id']) ? 'updated' : 'created';
        $orderArr = array(
            'status' => $request['status'],
            'order_number' => $request['item_name'],
            'location_id' => $request['location_id'],
            'user_id' => auth()->id()
        );
        $orderDetailsArr = array(
            'sku' => $request['sku'],
            'item_name' => $request['item_name'],
            'non_foc_quantity' => $request['non_foc_quantity'],
            'foc_quantity' => $request['foc_quantity'],
            'foc_quantity' => $request['foc_quantity'],
            'total_quantity' => $request['total_quantity'],
            'uom' => $request['uom'],
            'price' => $request['price'],
            'line_price' => $request['line_price'],
        );
        $order = Order::updateOrCreate(['id' => $request['id']], $orderArr);
        $order->order_details()->create($orderDetailsArr);

        return response()->json([
            "message" => "Item has been ".$msg
        ], 200);
    }

}
