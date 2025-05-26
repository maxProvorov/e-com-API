<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_and_retrieved()
    {
        $product = Product::create([
            'name' => 'Тестовый продукт',
            'description' => 'Описание',
            'price' => 123.45,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Тестовый продукт',
            'price' => 123.45,
        ]);
        
        $found = Product::find($product->id);

        $this->assertNotNull($found);
        
        $this->assertEquals('Тестовый продукт', $found->name);
    }
}
