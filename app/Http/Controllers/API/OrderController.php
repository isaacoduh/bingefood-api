<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
           DB::beginTransaction();
           $order = new Order();
           $order->email = $request->input('email');
           $order->save();
        //    $lineItems = [];
           foreach($request->input('products') as $item) {
               $product = Product::find($item['product_id']);
               $orderItem = new OrderItem();

               $orderItem->order_id = $order->id;
               $orderItem->product_title = $product->name;
               $orderItem->price = $product->price;
               $orderItem->quantity = $item['quantity'];
               $orderItem->subtotal = $product->price * $item['quantity'];

               $orderItem->save();

           }
           DB::commit();
           return response()->json([
               "status" => true,
               "order" => $order->load('orderItems'),
               "total" => $order->total
           ]);

        } catch (\Throwable $e) {
            //throw $th;
            DB::rollBack();
            return response([
                'error' => $e->getMessage()
            ],400); 
        }
    }
}
