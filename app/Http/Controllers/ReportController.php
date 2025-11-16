<?php

namespace App\Http\Controllers;

use App\Repositories\Eloquent\TransactionRepository;
use Illuminate\Http\Request;

class ReportController extends Controller
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
        $year = $request->query('year', $now->year);
        $month = $request->query('month', $now->month);

        $summary = $this->transactionRepository->monthlySummary($user, $year, $month);
        $byCategory = $this->transactionRepository->sumByCategory(
            $user,
            \Carbon\Carbon::create($year, $month, 1)->startOfDay(),
            \Carbon\Carbon::create($year, $month, 1)->endOfMonth()
        );

        // Prepare budget comparison
        $budgetComparison = [];
        foreach ($user->budgets()->where('year', $year)->where('month', $month)->with('category')->get() as $b) {
            $categoryName = $b->category->name;
            $spent = $byCategory[$categoryName] ?? 0;
            $budgetComparison[$categoryName] = [
                'budget' => (float) $b->limit,
                'spent' => $spent,
                'remaining' => (float) $b->limit - $spent,
                'percentage' => ($spent / (float) $b->limit) * 100,
            ];
        }

        return view('reports.index', compact('summary', 'byCategory', 'budgetComparison', 'year', 'month'));
    }
}
