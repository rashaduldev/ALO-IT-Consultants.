<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['sku' => 'POS-001', 'name' => 'A4 Copy Paper (500 Sheets)', 'price' => 650.00, 'stock_quantity' => 50],
            ['sku' => 'POS-002', 'name' => 'Ballpoint Pen (Blue)', 'price' => 25.00, 'stock_quantity' => 200],
            ['sku' => 'POS-003', 'name' => 'Desktop Stapler', 'price' => 220.00, 'stock_quantity' => 35],
            ['sku' => 'POS-004', 'name' => 'Staple Pins No. 10', 'price' => 45.00, 'stock_quantity' => 4],
            ['sku' => 'POS-005', 'name' => 'USB Keyboard', 'price' => 950.00, 'stock_quantity' => 18],
            ['sku' => 'POS-006', 'name' => 'Wireless Mouse', 'price' => 780.00, 'stock_quantity' => 3],
            ['sku' => 'POS-007', 'name' => '24-inch LED Monitor', 'price' => 16500.00, 'stock_quantity' => 8],
            ['sku' => 'POS-008', 'name' => 'USB-C Charging Cable', 'price' => 350.00, 'stock_quantity' => 60],
            ['sku' => 'POS-009', 'name' => 'Notebook A5', 'price' => 120.00, 'stock_quantity' => 100],
            ['sku' => 'POS-010', 'name' => 'Thermal Receipt Roll', 'price' => 90.00, 'stock_quantity' => 2],
            ['sku' => 'POS-011', 'name' => 'Calculator', 'price' => 1250.00, 'stock_quantity' => 15],
            ['sku' => 'POS-012', 'name' => 'Desk Organizer', 'price' => 480.00, 'stock_quantity' => 25],
            ['sku' => 'POS-013', 'name' => 'Laser Toner Cartridge', 'price' => 4200.00, 'stock_quantity' => 5],
            ['sku' => 'POS-014', 'name' => 'Whiteboard Marker Set', 'price' => 320.00, 'stock_quantity' => 40],
            ['sku' => 'POS-015', 'name' => 'Power Strip (6 Socket)', 'price' => 1100.00, 'stock_quantity' => 1],
        ] as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                $product,
            );
        }
    }
}
