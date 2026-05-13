<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['Cash', 'cash'],
            ['Credit Card', 'card'],
            ['Bank Transfer', 'transfer'],
            ['ABA Pay', 'aba'],
            ['Wing', 'wing'],
        ];
        foreach ($methods as [$name, $code]) {
            PaymentMethod::firstOrCreate(
                ['company_id' => null, 'code' => $code],
                ['name' => $name, 'status' => 'active']
            );
        }
    }
}
