<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Aarong Traders', 'email' => 'contact@aarongtraders.test', 'phone' => '01711000001', 'address' => 'Dhanmondi, Dhaka'],
            ['name' => 'Bangla Mart', 'email' => 'sales@banglamart.test', 'phone' => '01711000002', 'address' => 'Mirpur, Dhaka'],
            ['name' => 'Chattogram Supplies', 'email' => 'orders@ctgsupplies.test', 'phone' => '01711000003', 'address' => 'Agrabad, Chattogram'],
            ['name' => 'Delta Fashion House', 'email' => 'hello@deltafashion.test', 'phone' => '01711000004', 'address' => 'Uttara, Dhaka'],
            ['name' => 'Eastern Electronics', 'email' => 'info@easternelectronics.test', 'phone' => '01711000005', 'address' => 'GEC Circle, Chattogram'],
            ['name' => 'Fresh Choice Grocery', 'email' => 'orders@freshchoice.test', 'phone' => '01711000006', 'address' => 'Khilgaon, Dhaka'],
            ['name' => 'Green Leaf Cafe', 'email' => 'manager@greenleafcafe.test', 'phone' => '01711000007', 'address' => 'Banani, Dhaka'],
            ['name' => 'Horizon Office Solutions', 'email' => 'accounts@horizonoffice.test', 'phone' => '01711000008', 'address' => 'Motijheel, Dhaka'],
            ['name' => 'Ideal Book Store', 'email' => 'owner@idealbooks.test', 'phone' => '01711000009', 'address' => 'New Market, Dhaka'],
            ['name' => 'Jatra Enterprise', 'email' => 'admin@jatraenterprise.test', 'phone' => '01711000010', 'address' => 'Sylhet Sadar, Sylhet'],
        ] as $customer) {
            Customer::query()->updateOrCreate(
                ['email' => $customer['email']],
                $customer,
            );
        }
    }
}
