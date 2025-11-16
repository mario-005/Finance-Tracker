<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Models\User;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Carbon\Carbon;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function paginateForUser(User $user, array $filters = [], int $perPage = 15)
    {
        $query = $user->transactions()->with('category');

        if ($filters['type'] ?? null) {
            $query->where('type', $filters['type']);
        }

        if ($filters['category_id'] ?? null) {
            $query->where('category_id', $filters['category_id']);
        }

        // Full text-ish search across description and category name
        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(function($q) use ($term) {
                $q->where('description', 'like', "%{$term}%")
                  ->orWhereHas('category', function($q2) use ($term) {
                      $q2->where('name', 'like', "%{$term}%");
                  });
            });
        }

        if ($filters['from_date'] ?? null && $filters['to_date'] ?? null) {
            $query->whereBetween('date', [$filters['from_date'], $filters['to_date']]);
        }

        return $query->orderByDesc('date')->paginate($perPage);
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);
        return $transaction;
    }

    public function delete(Transaction $transaction): bool
    {
        return $transaction->delete();
    }

    public function sumByCategory(User $user, Carbon $from, Carbon $to): array
    {
        $result = [];
        $transactions = $user->transactions()
            ->whereBetween('date', [$from, $to])
            ->with('category')
            ->get();

        foreach ($transactions as $tx) {
            $categoryName = $tx->category->name ?? 'Uncategorized';
            if (!isset($result[$categoryName])) {
                $result[$categoryName] = 0;
            }
            $result[$categoryName] += (float) $tx->amount;
        }

        return $result;
    }

    public function monthlySummary(User $user, int $year, int $month): array
    {
        $from = Carbon::create($year, $month, 1)->startOfDay();
        $to = (clone $from)->endOfMonth();

        $transactions = $user->transactions()
            ->whereBetween('date', [$from, $to])
            ->get();

        $income = 0;
        $expense = 0;

        foreach ($transactions as $tx) {
            if ($tx->type === 'income') {
                $income += (float) $tx->amount;
            } else {
                $expense += (float) $tx->amount;
            }
        }

        return [
            'income' => $income,
            'expense' => $expense,
            'saving' => $income - $expense,
        ];
    }

    public function getAllForUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->transactions()->with('category')->orderByDesc('date')->get();
    }
}
