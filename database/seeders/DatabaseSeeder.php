<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingGoal;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo user
        User::firstOrCreate(
            ['email' => 'demo@demo.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        $accounts = collect([
            ['name' => 'BCA Utama', 'type' => 'bank', 'opening_balance' => 0, 'color' => '#2563eb'],
            ['name' => 'E-Wallet', 'type' => 'ewallet', 'opening_balance' => 0, 'color' => '#7c3aed'],
            ['name' => 'Kas Darurat', 'type' => 'saving', 'opening_balance' => 0, 'color' => '#059669'],
        ])->map(fn ($account) => Account::create($account));

        $categories = collect([
            ['name' => 'Gaji', 'type' => 'income', 'icon' => 'briefcase', 'color' => '#16a34a'],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => 'sparkles', 'color' => '#0891b2'],
            ['name' => 'Makan', 'type' => 'expense', 'icon' => 'utensils', 'color' => '#f97316'],
            ['name' => 'Transportasi', 'type' => 'expense', 'icon' => 'car', 'color' => '#0ea5e9'],
            ['name' => 'Tagihan', 'type' => 'expense', 'icon' => 'receipt', 'color' => '#ef4444'],
            ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'shopping-bag', 'color' => '#a855f7'],
            ['name' => 'Investasi', 'type' => 'expense', 'icon' => 'chart', 'color' => '#14b8a6'],
            ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'music', 'color' => '#eab308'],
        ])->mapWithKeys(fn ($category) => [$category['name'] => Category::create($category)]);
    }
}
