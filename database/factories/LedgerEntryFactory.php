<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\LedgerEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LedgerEntry>
 */
class LedgerEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'journal_entry_id' => JournalEntry::factory(),
            'journal_entry_line_id' => JournalEntryLine::factory(),
            'debit' => 100,
            'credit' => 0,
            'running_balance' => 100,
        ];
    }
}
