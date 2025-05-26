<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\CancelUnpaidOrders;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id());
        
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('sort')) {
            $sort = $request->get('sort');
            if ($sort === 'date_asc') $query->orderBy('created_at');
            if ($sort === 'date_desc') $query->orderByDesc('created_at');
        }
        
        return response()->json($query->with('items.product', 'paymentMethod')->get());
    }

    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())->with('items.product', 'paymentMethod')->findOrFail($id);
        
        return response()->json($order);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required',
        ]);

        $cart = Cart::where('user_id', Auth::id())->with('items.product')->firstOrFail();
        
        if ($cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $total = $cart->items->sum(fn($item) => $item->product->price * $item->quantity);
        
        DB::beginTransaction();

        $order = Order::create([
            'user_id' => Auth::id(),
            'payment_method_id' => $request->payment_method_id,
            'status' => 'pending',
            'total' => $total,
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        $cart->items()->delete();
        $cart->delete();

        CancelUnpaidOrders::dispatch($order->id)->delay(now()->addMinutes(2));
        
        DB::commit();

        $paymentMethod = PaymentMethod::find($request->payment_method_id);

        $callbackUrl = route('payment.callback', ['order' => $order->id]);

        $paymentUrl = str_replace(
            ['{order_id}', '{amount}', '{callback_url}'],
            [$order->id, $order->total, urlencode($callbackUrl)],
            $paymentMethod->payment_url_template
        );

        return response()->json([
            'order' => $order,
            'payment_url' => $paymentUrl,
        ]);
    }
}
