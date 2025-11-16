<?php

namespace App\Http\Controllers;

use App\Repositories\Eloquent\TransactionRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $now = now();

        // This month summary
        $summary = $this->transactionRepository->monthlySummary($user, $now->year, $now->month);

        // Category breakdown
        $byCategory = $this->transactionRepository->sumByCategory(
            $user,
            $now->clone()->startOfMonth(),
            $now->clone()->endOfMonth()
        );

        // Recent transactions
        $recentTransactions = $user->transactions()
            ->with('category')
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        // Budget alerts
        $budgetAlerts = [];
        foreach ($user->budgets()->where('year', $now->year)->where('month', $now->month)->with('category')->get() as $b) {
            $categoryName = $b->category->name;
            $spent = $byCategory[$categoryName] ?? 0;
            if ($spent > (float) $b->limit) {
                $budgetAlerts[] = [
                    'category' => $categoryName,
                    'budget' => (float) $b->limit,
                    'spent' => $spent,
                    'overage' => $spent - (float) $b->limit,
                ];
            }
        }

        return view('dashboard', compact('summary', 'byCategory', 'recentTransactions', 'budgetAlerts'));
    }
}
