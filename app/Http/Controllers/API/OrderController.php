<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cartalyst\Stripe\Stripe;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
           DB::beginTransaction();
           $order = new Order();
           $order->email = $request->input('email');
           $order->save();
           $lineItems = [];
           foreach($request->input('products') as $item) {
               $product = Product::find($item['product_id']);
               $orderItem = new OrderItem();

               $orderItem->order_id = $order->id;
               $orderItem->product_title = $product->name;
               $orderItem->price = $product->price;
               $orderItem->quantity = $item['quantity'];
               $orderItem->subtotal = $product->price * $item['quantity'];

               $orderItem->save();
            
               $lineItems[] = [
                   'name' => $product->name,
                   'description' => 'product',
                   'images' => [
                       $product->image
                   ],
                   'amount' => 100 * $product->price,
                   'currency' => 'usd',
                   'quantity' => $item['quantity']
                ];
           }

           $stripe = Stripe::make(env('STRIPE_SECRET'));
           $source = $stripe->checkout()->sessions()->create([
               'payment_method_types' => ['card'],
               'line_items' => $lineItems,
               'success_url' => env('CHECKOUT_URL') . '/success?source={CHECKOUT_SESSION_ID}',
               'cancel_url' => env('CHECKOUT_URL'). '/error'
           ]);

           $order->transaction_id = $source['id'];
           $order->save();
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
    
    public function confirm(Request $request)
    {
        if(!$order = Order::where('transaction_id', $request->input('source'))->first()) {
            return response([
                'error' => 'Order not found !'
            ], 404);
        }

        $order->complete = 1;
        $order->save();

        // event or notification
        return [
            'message' => 'success'
        ];
    }
}
