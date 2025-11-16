<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Budget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('password123'),
            ]
        );

        // Create categories
        $categories = [
            ['name' => 'Makan & Minum', 'type' => 'expense'],
            ['name' => 'Transport', 'type' => 'expense'],
            ['name' => 'Tagihan', 'type' => 'expense'],
            ['name' => 'Belanja', 'type' => 'expense'],
            ['name' => 'Hiburan', 'type' => 'expense'],
            ['name' => 'Kesehatan', 'type' => 'expense'],
            ['name' => 'Pendapatan Utama', 'type' => 'income'],
            ['name' => 'Bonus', 'type' => 'income'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $cat['name']],
                ['type' => $cat['type']]
            );
        }

        $userCategories = $user->categories;
        $now = now();

        // Create sample transactions for this month
        $sampleTransactions = [
            ['type' => 'income', 'amount' => 12000000, 'date' => $now->clone()->setDay(1), 'desc' => 'Gaji Bulan ' . $now->format('F'), 'cat' => 'Pendapatan Utama'],
            ['type' => 'expense', 'amount' => 50000, 'date' => $now->clone()->setDay(2), 'desc' => 'Sarapan', 'cat' => 'Makan & Minum'],
            ['type' => 'expense', 'amount' => 2000000, 'date' => $now->clone()->setDay(5), 'desc' => 'Belanja bulanan di supermarket', 'cat' => 'Belanja'],
            ['type' => 'expense', 'amount' => 150000, 'date' => $now->clone()->setDay(5), 'desc' => 'Bensin', 'cat' => 'Transport'],
            ['type' => 'expense', 'amount' => 2000000, 'date' => $now->clone()->setDay(7), 'desc' => 'Pembayaran Listrik dan Air', 'cat' => 'Tagihan'],
            ['type' => 'expense', 'amount' => 100000, 'date' => $now->clone()->setDay(10), 'desc' => 'Makan malam bersama keluarga', 'cat' => 'Makan & Minum'],
            ['type' => 'expense', 'amount' => 500000, 'date' => $now->clone()->setDay(12), 'desc' => 'Hiburan: Bioskop', 'cat' => 'Hiburan'],
            ['type' => 'expense', 'amount' => 200000, 'date' => $now->clone()->setDay(15), 'desc' => 'Obat dan vitamin', 'cat' => 'Kesehatan'],
            ['type' => 'expense', 'amount' => 150000, 'date' => $now->clone()->setDay(18), 'desc' => 'Makan siang', 'cat' => 'Makan & Minum'],
            ['type' => 'income', 'amount' => 500000, 'date' => $now->clone()->setDay(20), 'desc' => 'Bonus Performa Kerja', 'cat' => 'Bonus'],
            ['type' => 'expense', 'amount' => 300000, 'date' => $now->clone()->setDay(22), 'desc' => 'Belanja pakaian', 'cat' => 'Belanja'],
            ['type' => 'expense', 'amount' => 100000, 'date' => $now->clone()->setDay(25), 'desc' => 'Kopi di café', 'cat' => 'Makan & Minum'],
            ['type' => 'expense', 'amount' => 75000, 'date' => $now->clone()->setDay(28), 'desc' => 'Pulsa Internet', 'cat' => 'Tagihan'],
        ];

        foreach ($sampleTransactions as $tx) {
            $category = $userCategories->where('name', $tx['cat'])->first();
            Transaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $tx['date'],
                    'amount' => $tx['amount'],
                    'type' => $tx['type'],
                    'description' => $tx['desc'],
                ],
                ['category_id' => $category?->id]
            );
        }

        // Create budgets for this month
        $budgets = [
            ['cat' => 'Makan & Minum', 'limit' => 2000000],
            ['cat' => 'Transport', 'limit' => 700000],
            ['cat' => 'Tagihan', 'limit' => 2000000],
            ['cat' => 'Belanja', 'limit' => 2500000],
            ['cat' => 'Hiburan', 'limit' => 500000],
        ];

        foreach ($budgets as $b) {
            $category = $userCategories->where('name', $b['cat'])->first();
            if ($category) {
                Budget::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                        'year' => $now->year,
                        'month' => $now->month,
                    ],
                    ['limit' => $b['limit']]
                );
            }
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Email: demo@example.com, Password: password123');
    }
}
