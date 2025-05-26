<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::insert([
            [
                'name' => 'method1',
                'payment_url_template' => 'https://pay.method1.com/pay?order={order_id}&amount={amount}&callback={callback_url}',
            ],
            [
                'name' => 'method2',
                'payment_url_template' => 'https://pay.method2.com/pay?order={order_id}&amount={amount}&callback={callback_url}',
            ],
            [
                'name' => 'method3',
                'payment_url_template' => 'https://pay.method3.com/pay?order={order_id}&amount={amount}&callback={callback_url}',
            ],
        ]);
    }
}
