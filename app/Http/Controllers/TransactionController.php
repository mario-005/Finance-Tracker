<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Repositories\Eloquent\TransactionRepository;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        // Ensure filter keys exist to avoid undefined index in views
        $defaults = ['type' => '', 'category_id' => '', 'from_date' => '', 'to_date' => '', 'q' => ''];
        $filters = array_merge($defaults, $request->only(['type', 'category_id', 'from_date', 'to_date', 'q']));
        $transactions = $this->transactionRepository->paginateForUser($user, $filters);
        $categories = $user->categories;

        return view('transactions.index', compact('transactions', 'categories', 'filters'));
    }

    public function create(Request $request)
    {
        $incomeCategories = $request->user()->categories()->where('type', 'income')->get();
        $expenseCategories = $request->user()->categories()->where('type', 'expense')->get();
        return view('transactions.create', compact('incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $data['user_id'] = $user->id;

        // If user provided a category name and didn't select an existing category, find or create it
        if (empty($data['category_id']) && !empty($data['category_name'])) {
            $cat = Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $data['category_name']],
                ['type' => $data['type']]
            );
            $data['category_id'] = $cat->id;
        }

        $this->transactionRepository->create($data);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Request $request, Transaction $transaction)
    {
        // Check if user owns this transaction
        if ($transaction->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
        $incomeCategories = $request->user()->categories()->where('type', 'income')->get();
        $expenseCategories = $request->user()->categories()->where('type', 'expense')->get();
        return view('transactions.edit', compact('transaction', 'incomeCategories', 'expenseCategories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        // Check if user owns this transaction
        if ($transaction->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
        $data = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // If user provided a category name and did not select an existing id, find/create it
        $user = $request->user();
        if (empty($data['category_id']) && !empty($data['category_name'])) {
            $cat = Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $data['category_name']],
                ['type' => $data['type']]
            );
            $data['category_id'] = $cat->id;
        }

        $this->transactionRepository->update($transaction, $data);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        // Check if user owns this transaction
        if ($transaction->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
        $this->transactionRepository->delete($transaction);
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
