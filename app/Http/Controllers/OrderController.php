<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function saveOrder(Request $request)
    {
        $msg = isset($request['id']) ? 'updated' : 'created';
        $orderArr = array(
            'status' => $request['status'],
            'order_number' => $request['item_name'],
            'location_code' => $request['location_code'],
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
