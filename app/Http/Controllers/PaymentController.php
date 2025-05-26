<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    public function callback(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Order cannot be updated. Invalid status.',
                'current_status' => $order->status,
            ], 422);
        }

        $order->status = 'paid';
        $order->paid_at = now();
        $order->save();

        return response()->json(['message' => 'Order status updated']);
    }
}
