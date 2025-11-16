<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Budget;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the first user
        $user = User::first() ?? User::factory()->create([
            'name' => 'Mario',
            'email' => 'mario@example.com',
            'password' => bcrypt('password')
        ]);

        // Create categories
        $categories = [
            ['name' => 'Makanan', 'type' => 'expense'],
            ['name' => 'Transportasi', 'type' => 'expense'],
            ['name' => 'Hiburan', 'type' => 'expense'],
            ['name' => 'Tagihan', 'type' => 'expense'],
            ['name' => 'Kesehatan', 'type' => 'expense'],
            ['name' => 'Gaji', 'type' => 'income'],
            ['name' => 'Freelance', 'type' => 'income'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $cat['name']],
                ['type' => $cat['type']]
            );
        }

        // Create demo transactions for this month
        $now = now();
        $startOfMonth = $now->clone()->startOfMonth();

        $transactions = [
            ['date' => $startOfMonth->copy()->addDays(1), 'category' => 'Gaji', 'description' => 'Gaji bulanan', 'amount' => 5000000, 'type' => 'income'],
            ['date' => $startOfMonth->copy()->addDays(2), 'category' => 'Makanan', 'description' => 'Belanja di supermarket', 'amount' => 250000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(3), 'category' => 'Transportasi', 'description' => 'Bensin', 'amount' => 150000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(4), 'category' => 'Tagihan', 'description' => 'Listrik dan air', 'amount' => 450000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(5), 'category' => 'Hiburan', 'description' => 'Nonton bioskop', 'amount' => 100000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(6), 'category' => 'Makanan', 'description' => 'Makan siang', 'amount' => 80000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(8), 'category' => 'Kesehatan', 'description' => 'Obat dan vitamin', 'amount' => 200000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(10), 'category' => 'Freelance', 'description' => 'Proyek desain grafis', 'amount' => 1500000, 'type' => 'income'],
            ['date' => $startOfMonth->copy()->addDays(12), 'category' => 'Transportasi', 'description' => 'Ojek online', 'amount' => 120000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(15), 'category' => 'Makanan', 'description' => 'Restoran malam', 'amount' => 350000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(18), 'category' => 'Hiburan', 'description' => 'Game subscription', 'amount' => 99000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(20), 'category' => 'Tagihan', 'description' => 'Internet bulanan', 'amount' => 300000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(22), 'category' => 'Makanan', 'description' => 'Kopi dan pastry', 'amount' => 75000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(25), 'category' => 'Kesehatan', 'description' => 'Check up dokter', 'amount' => 500000, 'type' => 'expense'],
            ['date' => $startOfMonth->copy()->addDays(28), 'category' => 'Transportasi', 'description' => 'Parkir bulanan', 'amount' => 200000, 'type' => 'expense'],
        ];

        foreach ($transactions as $tx) {
            Transaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'category_id' => $categoryModels[$tx['category']]->id,
                    'date' => $tx['date'],
                    'description' => $tx['description'],
                ],
                [
                    'amount' => $tx['amount'],
                    'type' => $tx['type'],
                ]
            );
        }

        // Create budgets for this month
        $budgets = [
            ['category' => 'Makanan', 'limit' => 800000],
            ['category' => 'Transportasi', 'limit' => 500000],
            ['category' => 'Hiburan', 'limit' => 300000],
            ['category' => 'Tagihan', 'limit' => 1000000],
            ['category' => 'Kesehatan', 'limit' => 400000],
        ];

        foreach ($budgets as $budget) {
            Budget::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'category_id' => $categoryModels[$budget['category']]->id,
                    'year' => $now->year,
                    'month' => $now->month,
                ],
                [
                    'limit' => $budget['limit'],
                ]
            );
        }

        echo "✅ Demo data seeded successfully!\n";
    }
}
