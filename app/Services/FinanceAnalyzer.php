<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Carbon\Carbon;

class FinanceAnalyzer
{
    protected $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function buildMonthlyPayload(User $user, int $year, int $month): array
    {
        $from = Carbon::create($year, $month, 1)->startOfDay();
        $to = (clone $from)->endOfMonth();

        // Expect repository to provide these helper methods
        $summary = $this->transactionRepository->monthlySummary($user, $year, $month);
        $byCategory = $this->transactionRepository->sumByCategory($user, $from, $to);

        $budgets = [];
        if (method_exists($user, 'budgets')) {
            foreach ($user->budgets()->where('year', $year)->where('month', $month)->get() as $b) {
                $budgets[$b->category->name ?? $b->category_id] = (float) $b->limit;
            }
        }

        return [
            'month' => $from->format('Y-m'),
            'income' => (float) ($summary['income'] ?? 0),
            'expense' => (float) ($summary['expense'] ?? 0),
            'by_category' => $byCategory,
            'budget' => $budgets,
        ];
    }
}
