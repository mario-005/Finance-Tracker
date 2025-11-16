<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $now = now();
        $budgets = $user->budgets()
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->with('category')
            ->get();

        return view('budgets.index', compact('budgets'));
    }

    public function create(Request $request)
    {
        $categories = $request->user()->categories;
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'limit' => 'required|numeric|min:0.01',
        ]);

        $now = now();
        Budget::updateOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'year' => $now->year,
                'month' => $now->month,
            ],
            ['limit' => $data['limit']]
        );

        return redirect()->route('budgets.index')->with('success', 'Budget berhasil diatur.');
    }

    public function destroy(Request $request, Budget $budget)
    {
        if ($budget->user_id !== $request->user()->id) {
            abort(403);
        }
        $budget->delete();
        return redirect()->route('budgets.index')->with('success', 'Budget dihapus.');
    }
}
