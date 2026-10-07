<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        Account::query()->upsert([
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset'],
            ['code' => '2100', 'name' => 'Tax Payable', 'type' => 'liability'],
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue'],
        ], ['code'], ['name', 'type']);
    }
}
