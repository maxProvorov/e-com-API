<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            [
                'name' => 'Смартфон',
                'description' => 'Современный смартфон с большим экраном',
                'price' => 29999.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ноутбук',
                'description' => 'Мощный ноутбук для работы и игр',
                'price' => 79999.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Наушники',
                'description' => 'Беспроводные наушники с шумоподавлением',
                'price' => 5999.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Клавиатура',
                'description' => 'Механическая клавиатура с подсветкой',
                'price' => 3499.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Монитор',
                'description' => '27-дюймовый IPS монитор',
                'price' => 15999.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
