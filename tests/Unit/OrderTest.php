<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Cart;
use App\Models\CartItem;
use App\Jobs\CancelUnpaidOrders;
use Illuminate\Support\Facades\Bus;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_is_created_and_cancel_job_dispatched()
    {
        Bus::fake();

        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Тестовый товар',
            'description' => 'desc',
            'price' => 100,
        ]);

        $payment = PaymentMethod::create([
            'name' => 'TestPay',
            'payment_url_template' => 'https://pay.test/{order_id}'
        ]);

        $this->actingAs($user);

        $cart = Cart::create(['user_id' => $user->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
        
        $response = $this->postJson('/api/cart/checkout', [
            'payment_method_id' => $payment->id
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Bus::assertDispatched(CancelUnpaidOrders::class);
    }

    public function test_order_is_cancelled_by_job()
    {
        $user = User::factory()->create();

        $payment = PaymentMethod::create([
            'name' => 'TestPay',
            'payment_url_template' => 'https://pay.test/{order_id}'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'payment_method_id' => $payment->id,
            'status' => 'pending',
            'total' => 100,
            'created_at' => now()->subMinutes(3),
        ]);

        (new CancelUnpaidOrders($order->id))->handle();

        $order->refresh();

        $this->assertEquals('cancelled', $order->status);
    }

    public function test_order_can_be_paid_via_callback()
    {
        $user = User::factory()->create();

        $payment = PaymentMethod::create([
            'name' => 'TestPay',
            'payment_url_template' => 'https://pay.test/{order_id}'
        ]);
        
        $order = Order::create([
            'user_id' => $user->id,
            'payment_method_id' => $payment->id,
            'status' => 'pending',
            'total' => 100,
        ]);

        $response = $this->postJson("/api/payment/callback/{$order->id}");
        $response->assertStatus(200);
        $order->refresh();

        $this->assertEquals('paid', $order->status);
    }

    public function test_cart_is_deleted_after_checkout()
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Товар',
            'description' => 'desc',
            'price' => 100,
        ]);
        
        $payment = PaymentMethod::create([
            'name' => 'TestPay',
            'payment_url_template' => 'https://pay.test/{order_id}'
        ]);
        $this->actingAs($user);

        $cart = Cart::create(['user_id' => $user->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->postJson('/api/cart/checkout', [
            'payment_method_id' => $payment->id
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id
        ]);
    }
}
