<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_product_to_cart()
    {
        $user = User::factory()->create();
        
        $product = Product::create([
            'name' => 'Товар',
            'description' => 'desc',
            'price' => 100,
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id
        ]);
    }

    public function test_remove_product_from_cart()
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Товар',
            'description' => 'desc',
            'price' => 100,
        ]);
        $cart = \App\Models\Cart::create(['user_id' => $user->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/cart/remove', [
            'product_id' => $product->id
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id
        ]);
    }
}
