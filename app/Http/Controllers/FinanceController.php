<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\MonthlyBudget;
use App\Models\SavingGoal;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    public function dashboard(Request $request)
    {
        $selectedMonth = Carbon::createFromFormat('Y-m', $request->query('month', now()->format('Y-m')))->startOfMonth();
        $start = $selectedMonth->copy()->startOfMonth();
        $end = $selectedMonth->copy()->endOfMonth();

        $transactions = Transaction::with(['account', 'category'])
            ->latest('transaction_date')
            ->latest()
            ->get();

        $monthlyTransactions = $transactions->filter(fn (Transaction $transaction) => $transaction->transaction_date->betweenIncluded($start, $end));
        $income = (float) $monthlyTransactions->where('type', 'income')->sum('amount');
        $expense = (float) $monthlyTransactions->where('type', 'expense')->sum('amount');
        $balance = Account::with('transactions')->get()->sum(fn (Account $account) => $account->balance);
        $savingsRate = $income > 0 ? round((($income - $expense) / $income) * 100, 1) : 0;

        $categoryExpenses = $monthlyTransactions
            ->where('type', 'expense')
            ->groupBy('category_id')
            ->map(function ($items) use ($expense) {
                $category = $items->first()->category;
                $total = (float) $items->sum('amount');

                return [
                    'name' => $category->name,
                    'color' => $category->color,
                    'total' => $total,
                    'percentage' => $expense > 0 ? round(($total / $expense) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $cashflow = collect(range(5, 0))->map(function (int $monthsAgo) use ($transactions) {
            $month = now()->subMonths($monthsAgo)->startOfMonth();
            $items = $transactions->filter(fn (Transaction $transaction) => $transaction->transaction_date->isSameMonth($month));

            return [
                'label' => $month->translatedFormat('M Y'),
                'income' => (float) $items->where('type', 'income')->sum('amount'),
                'expense' => (float) $items->where('type', 'expense')->sum('amount'),
            ];
        });

        $budgets = $this->getBudgetsWithProgress($selectedMonth, $monthlyTransactions);
        
        // Advanced KPIs
        $budgetCompliance = $budgets->count() > 0 ? round(100 - $budgets->avg('progress')) : 100;
        
        $prevMonth = $selectedMonth->copy()->subMonth();
        $prevMonthTransactions = Transaction::whereYear('transaction_date', $prevMonth->year)
            ->whereMonth('transaction_date', $prevMonth->month)
            ->where('type', 'expense')
            ->sum('amount');
        $expenseTrend = $prevMonthTransactions > 0 
            ? round((($expense - $prevMonthTransactions) / $prevMonthTransactions) * 100, 1)
            : 0;

        $insights = $this->buildInsights($income, $expense, $categoryExpenses, $budgets, $savingsRate);

        $globalBudget = MonthlyBudget::where('month', $selectedMonth->month)
            ->where('year', $selectedMonth->year)->first();

        return view('finance.dashboard', [
            'selectedMonth' => $selectedMonth,
            'summary' => compact('income', 'expense', 'balance', 'savingsRate', 'budgetCompliance', 'expenseTrend'),
            'categoryExpenses' => $categoryExpenses,
            'cashflow' => $cashflow,
            'insights' => $insights,
            'recentTransactions' => $transactions->take(5),
            'savingGoals' => SavingGoal::orderBy('target_date')->get(),
            'globalBudget' => $globalBudget,
        ]);
    }

    public function analytics(Request $request)
    {
        $selectedMonth = Carbon::createFromFormat('Y-m', $request->query('month', now()->format('Y-m')))->startOfMonth();
        $start = $selectedMonth->copy()->startOfMonth();
        $end = $selectedMonth->copy()->endOfMonth();

        $transactions = Transaction::with(['account', 'category'])
            ->whereBetween('transaction_date', [$selectedMonth->copy()->subMonths(5)->startOfMonth(), $end])
            ->oldest('transaction_date')
            ->get();

        $monthlyTransactions = $transactions->filter(fn (Transaction $transaction) => $transaction->transaction_date->betweenIncluded($start, $end));
        $monthlyIncome = (float) $monthlyTransactions->where('type', 'income')->sum('amount');
        $monthlyExpense = (float) $monthlyTransactions->where('type', 'expense')->sum('amount');
        $monthlyNet = $monthlyIncome - $monthlyExpense;
        $balance = Account::with('transactions')->get()->sum(fn (Account $account) => $account->balance);

        $series = collect(range(5, 0))->map(function (int $monthsAgo) use ($selectedMonth, $transactions) {
            $month = $selectedMonth->copy()->subMonths($monthsAgo)->startOfMonth();
            $items = $transactions->filter(fn (Transaction $transaction) => $transaction->transaction_date->isSameMonth($month));
            $income = (float) $items->where('type', 'income')->sum('amount');
            $expense = (float) $items->where('type', 'expense')->sum('amount');

            return [
                'label' => $month->translatedFormat('M Y'),
                'income' => $income,
                'expense' => $expense,
                'net' => $income - $expense,
            ];
        });

        $budgets = $this->getBudgetsWithProgress($selectedMonth, $monthlyTransactions);

        $categoryAnalysis = $monthlyTransactions
            ->where('type', 'expense')
            ->groupBy('category_id')
            ->map(function ($items) use ($monthlyExpense, $budgets) {
                $category = $items->first()->category;
                $budget = $budgets->firstWhere('category.id', $category->id);
                $total = (float) $items->sum('amount');
                $progress = $budget ? $budget['progress'] : null;

                return [
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'color' => $category->color,
                    'total' => $total,
                    'count' => $items->count(),
                    'percentage' => $monthlyExpense > 0 ? round(($total / $monthlyExpense) * 100, 1) : 0,
                    'budget_limit' => $budget['limit'] ?? null,
                    'budget_remaining' => $budget['remaining'] ?? null,
                    'budget_progress' => $progress,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $itemAnalysis = $monthlyTransactions
            ->where('type', 'expense')
            ->groupBy(fn ($item) => strtolower(trim($item->description)))
            ->map(function ($items, $description) use ($monthlyExpense) {
                $total = (float) $items->sum('amount');
                return [
                    'name' => ucwords($description),
                    'total' => $total,
                    'count' => $items->count(),
                    'percentage' => $monthlyExpense > 0 ? round(($total / $monthlyExpense) * 100, 1) : 0,
                    'category' => $items->first()->category->name,
                    'color' => $items->first()->category->color,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $insights = $this->buildSimplifiedInsights($monthlyIncome, $monthlyExpense, $categoryAnalysis, $budgets, $itemAnalysis);

        return view('finance.analytics', [
            'selectedMonth' => $selectedMonth,
            'summary' => [
                'balance' => $balance,
                'income' => $monthlyIncome,
                'expense' => $monthlyExpense,
                'net' => $monthlyNet,
            ],
            'series' => $series,
            'categoryAnalysis' => $categoryAnalysis,
            'budgets' => $budgets,
            'insights' => $insights,
            'itemAnalysis' => $itemAnalysis,
        ]);
    }

    public function transactions(Request $request)
    {
        $selectedMonth = Carbon::createFromFormat('Y-m', $request->query('month', now()->format('Y-m')))->startOfMonth();
        
        $transactions = Transaction::with(['account', 'category'])
            ->whereYear('transaction_date', $selectedMonth->year)
            ->whereMonth('transaction_date', $selectedMonth->month)
            ->latest('transaction_date')
            ->latest()
            ->paginate(15);

        $globalBudget = MonthlyBudget::where('month', $selectedMonth->month)
            ->where('year', $selectedMonth->year)->first();
            
        $totalExpense = Transaction::whereYear('transaction_date', $selectedMonth->year)
            ->whereMonth('transaction_date', $selectedMonth->month)
            ->where('type', 'expense')
            ->sum('amount');
            
        $remainingBudget = $globalBudget ? $globalBudget->amount - $totalExpense : null;

        return view('finance.transactions', [
            'transactions' => $transactions,
            'selectedMonth' => $selectedMonth,
            'accounts' => Account::orderBy('name')->get(),
            'categories' => Category::orderBy('type')->orderBy('name')->get(),
            'remainingBudget' => $remainingBudget,
        ]);
    }

    public function accounts()
    {
        return view('finance.accounts', [
            'accounts' => Account::with('transactions')->orderBy('name')->get(),
        ]);
    }

    public function budgets(Request $request)
    {
        $selectedMonth = Carbon::createFromFormat('Y-m', $request->query('month', now()->format('Y-m')))->startOfMonth();
        $start = $selectedMonth->copy()->startOfMonth();
        $end = $selectedMonth->copy()->endOfMonth();

        $monthlyTransactions = Transaction::whereBetween('transaction_date', [$start, $end])->get();
        $budgets = $this->getBudgetsWithProgress($selectedMonth, $monthlyTransactions);
        
        $globalBudget = MonthlyBudget::where('month', $selectedMonth->month)
            ->where('year', $selectedMonth->year)->first();
        $totalExpense = $monthlyTransactions->where('type', 'expense')->sum('amount');

        return view('finance.budgets', [
            'budgets' => $budgets,
            'selectedMonth' => $selectedMonth,
            'expenseCategories' => Category::where('type', 'expense')->orderBy('name')->get(),
            'globalBudget' => $globalBudget,
            'totalExpense' => $totalExpense,
        ]);
    }

    public function goals()
    {
        return view('finance.goals', [
            'savingGoals' => SavingGoal::orderBy('target_date')->get(),
        ]);
    }

    public function categories()
    {
        return view('finance.categories', [
            'categories' => Category::orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function storeCategory(Request $request)
    {
        Category::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:20'],
            'color' => ['nullable', 'string', 'max:20'],
        ]));

        return back()->with('status', 'Kategori baru ditambahkan.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $category->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:20'],
            'color' => ['nullable', 'string', 'max:20'],
        ]));

        return back()->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(Category $category)
    {
        try {
            $category->delete();
            return back()->with('status', 'Kategori berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['error' => 'Kategori tidak dapat dihapus karena sedang digunakan dalam transaksi atau anggaran.']);
        }
    }

    public function storeTransaction(Request $request)
    {
        Transaction::create($request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'merchant' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'is_recurring' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]) + ['is_recurring' => $request->boolean('is_recurring')]);

        return back()->with('status', 'Transaksi berhasil ditambahkan.');
    }

    public function destroyTransaction(Transaction $transaction)
    {
        $transaction->delete();
        return back()->with('status', 'Transaksi dihapus.');
    }

    public function storeAccount(Request $request)
    {
        Account::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'color' => ['required', 'string', 'max:20'],
        ]));

        return back()->with('status', 'Akun baru ditambahkan.');
    }

    public function storeBudget(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'limit_amount' => ['required', 'numeric', 'min:1'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
        ]);

        Budget::updateOrCreate(
            ['category_id' => $data['category_id'], 'month' => $data['month'], 'year' => $data['year']],
            ['limit_amount' => $data['limit_amount']]
        );

        return back()->with('status', 'Anggaran diperbarui.');
    }

    public function destroyBudget(Budget $budget)
    {
        $budget->delete();
        return back()->with('status', 'Anggaran kategori berhasil dihapus.');
    }

    public function storeMonthlyBudget(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
        ]);

        MonthlyBudget::updateOrCreate(
            ['month' => $data['month'], 'year' => $data['year']],
            ['amount' => $data['amount']]
        );

        return back()->with('status', 'Anggaran utama bulanan diperbarui.');
    }

    public function storeGoal(Request $request)
    {
        SavingGoal::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'target_date' => ['nullable', 'date'],
            'color' => ['required', 'string', 'max:20'],
        ]));

        return back()->with('status', 'Target tabungan baru berhasil dibuat.');
    }

    public function updateGoal(Request $request, SavingGoal $goal)
    {
        $data = $request->validate(['amount' => ['required', 'numeric', 'min:1']]);
        $goal->increment('current_amount', $data['amount']);

        return back()->with('status', 'Progress target tabungan bertambah.');
    }

    public function destroyGoal(SavingGoal $goal)
    {
        $goal->delete();
        return back()->with('status', 'Target tabungan telah dihapus.');
    }

    private function getBudgetsWithProgress($selectedMonth, $monthlyTransactions)
    {
        return Budget::with('category')
            ->where('month', $selectedMonth->month)
            ->where('year', $selectedMonth->year)
            ->get()
            ->map(function (Budget $budget) use ($monthlyTransactions) {
                $spent = (float) $monthlyTransactions
                    ->where('type', 'expense')
                    ->where('category_id', $budget->category_id)
                    ->sum('amount');

                return [
                    'id' => $budget->id,
                    'category' => $budget->category,
                    'limit' => (float) $budget->limit_amount,
                    'spent' => $spent,
                    'remaining' => (float) $budget->limit_amount - $spent,
                    'progress' => $budget->limit_amount > 0 ? min(100, round(($spent / (float) $budget->limit_amount) * 100)) : 0,
                ];
            });
    }

    private function buildInsights(float $income, float $expense, $categoryExpenses, $budgets, float $savingsRate): array
    {
        $insights = [];

        if ($income > 0 && $expense > $income * 0.8) {
            $insights[] = 'Pengeluaran sudah melewati 80% pemasukan bulan ini. Prioritaskan transaksi wajib dan tunda belanja fleksibel.';
        }

        if ($savingsRate >= 20) {
            $insights[] = 'Rasio tabungan sehat di atas 20%. Pertahankan pola ini untuk mempercepat target finansial.';
        } elseif ($income > 0) {
            $insights[] = 'Rasio tabungan masih rendah. Coba set auto-saving saat pemasukan masuk.';
        }

        $topCategory = $categoryExpenses->first();
        if ($topCategory) {
            $insights[] = "Kategori terbesar bulan ini adalah {$topCategory['name']} ({$topCategory['percentage']}%). Ini kandidat utama untuk dievaluasi.";
        }

        $overBudget = $budgets->first(fn ($budget) => $budget['remaining'] < 0);
        if ($overBudget) {
            $insights[] = "Anggaran {$overBudget['category']->name} sudah lewat batas. Kurangi pengeluaran kategori ini sampai bulan depan.";
        }

        return $insights ?: ['Data belum cukup untuk analisa mendalam. Tambahkan transaksi rutin agar rekomendasi lebih akurat.'];
    }

    private function buildSimplifiedInsights(float $income, float $expense, $categoryAnalysis, $budgets, $itemAnalysis = null): array
    {
        $insights = [];

        $savingsRate = $income > 0 ? round((($income - $expense) / $income) * 100, 1) : 0;

        if ($income > 0 && $expense > $income) {
            $insights[] = [
                'level' => 'danger',
                'title' => 'Pengeluaran melebihi pemasukan',
                'body' => 'Bulan ini pengeluaran Anda lebih besar dari pemasukan. Coba batasi pengeluaran non-esensial.',
            ];
        } elseif ($savingsRate >= 20) {
            $insights[] = [
                'level' => 'success',
                'title' => 'Tabungan dalam zona aman',
                'body' => "Rasio tabungan Anda bulan ini mencapai {$savingsRate}%. Pertahankan pola keuangan sehat ini!",
            ];
        } else {
            $insights[] = [
                'level' => 'warning',
                'title' => 'Rasio tabungan perlu ditingkatkan',
                'body' => "Rasio tabungan Anda saat ini {$savingsRate}%. Idealnya sisihkan minimal 20% untuk tabungan.",
            ];
        }

        $topCategory = $categoryAnalysis->first();
        if ($topCategory) {
            $insights[] = [
                'level' => 'info',
                'title' => 'Kategori pengeluaran tertinggi',
                'body' => "Kategori {$topCategory['name']} menyerap {$topCategory['percentage']}% dari total pengeluaran bulan ini.",
            ];
        }

        $overBudget = $budgets->first(fn ($budget) => $budget['remaining'] < 0);
        if ($overBudget) {
            $insights[] = [
                'level' => 'danger',
                'title' => 'Anggaran terlewati',
                'body' => "Pengeluaran untuk kategori {$overBudget['category']->name} sudah melewati anggaran yang ditentukan.",
            ];
        }
        
        if ($itemAnalysis && $itemAnalysis->isNotEmpty()) {
            $topItem = $itemAnalysis->first();
            if ($topItem['percentage'] > 10) {
                $insights[] = [
                    'level' => 'warning',
                    'title' => 'Pengeluaran Spesifik Tinggi',
                    'body' => "Anda menghabiskan {$topItem['percentage']}% pengeluaran bulan ini hanya untuk \"{$topItem['name']}\" (Rp " . number_format($topItem['total'], 0, ',', '.') . "). Pertimbangkan untuk mengevaluasinya.",
                ];
            }
        }

        return $insights ?: [[
            'level' => 'info',
            'title' => 'Belum ada data yang cukup',
            'body' => 'Tambahkan beberapa transaksi pemasukan dan pengeluaran untuk melihat rekomendasi otomatis.',
        ]];
    }
}
