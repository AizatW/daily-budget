<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;
use App\Http\Requests\ExpenseRequest;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'all');
        $payment_type = $request->input('payment_type', 'all');
        $month = $request->input('month', now()->format('Y-m'));

        $currentMonth = Carbon::createFromFormat('Y-m', $month);
        $monthLabel = $currentMonth->format('F Y');

        $previousMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');


        $query = $request->user()->expenses();

       
        
        [$year, $monthNumber] = explode('-', $month);
        $query->whereYear('input_date', $year);
        $query->whereMonth('input_date', $monthNumber);

        $monthlyExpenses = $request->user()->expenses()->whereYear('input_date', $year)->whereMonth('input_date', $monthNumber)->get();
        

        if ($type != "all"){
            $query->where('type', $type);
        }
        if ($payment_type != "all"){
            $query->where('payment_type', $payment_type);
        }
        
        $query->orderBy('input_date', 'desc')
              ->orderBy('id', 'desc');

        $search = trim($request->query('search', ''));

        if ($search != ''){
            $query->where('descriptions', 'LIKE', '%'. $search . '%');
        }

        $expenses = $query->paginate(10);

        $totalIncome = $monthlyExpenses->where('type', 'income') ->sum('amount');
        $totalExpense = $monthlyExpenses->where('type', 'expense') ->sum('amount');
        $balance = $totalIncome - $totalExpense;
        $transactionCount = $monthlyExpenses->count();
        $incomeCount = $monthlyExpenses->where('type', 'income') ->count();
        $expenseCount = $monthlyExpenses->where('type', 'expense') ->count();
        $averageExpense = $monthlyExpenses->where('type', 'expense') ->avg('amount');
        $dailyExpenses = $monthlyExpenses->where('type', 'expense')->groupBy('input_date');
        $dailyTotals = $dailyExpenses->map(function ($transactions){
            return $transactions->sum('amount');
        })->sortKeys();
        $cashIncome = $monthlyExpenses->where('payment_type', 'cash')->where('type', 'income')->sum('amount');
        $cashExpense = $monthlyExpenses->where('payment_type', 'cash')->where('type', 'expense')->sum('amount');
        $onlineIncome = $monthlyExpenses->where('payment_type', 'online')->where('type', 'income')->sum('amount');
        $onlineExpense = $monthlyExpenses->where('payment_type', 'online')->where('type', 'expense')->sum('amount');

        $cashBalance = $cashIncome - $cashExpense;
        $onlineBalance = $onlineIncome - $onlineExpense;

        return view('expenses', [
            'expenses' => $expenses,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'type' => $type,
            'payment_type' => $payment_type,
            'month' => $month,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
            'monthLabel' => $monthLabel,
            'transactionCount' => $transactionCount,
            'incomeCount' => $incomeCount,
            'expenseCount' => $expenseCount,
            'averageExpense' => $averageExpense,
            'dailyTotals' => $dailyTotals,
            'cashIncome' => $cashIncome,
            'cashExpense' => $cashExpense,
            'onlineIncome' => $onlineIncome,
            'onlineExpense' => $onlineExpense,
            'onlineBalance' => $onlineBalance,
            'cashBalance' => $cashBalance,
            'search' => $search
            ]);
        
    }

    public function store(ExpenseRequest $expenseRequest)
    {
        $validated = $expenseRequest->validated();

        $expenseRequest->user()->expenses()->create($validated);

        return redirect('/expenses')
            ->with('success', 'Transaction successfully added');
    }

    public function edit($id, Request $request)
    {
        $expense = $request->user()->expenses()->findOrFail($id);
        //$expense = Expense::findOrFail($id);

        return view('edit-expense', ['expense' => $expense]);
    }

    public function update(ExpenseRequest $expenseRequest, $id)
    {
        $expense = $expenseRequest->user()->expenses()->findOrFail($id);

        $validated = $expenseRequest->validated();

        $expense->update($validated);

        return redirect('/expenses')
            ->with('success', 'Transaction successfully updated');
    }

    public function destroy(Request $request, $id)
    {
        $expense = $request->user()->expenses()->findOrFail($id);

        $expense->delete();

        return redirect('/expenses')
            ->with('success', 'Transaction successfully deleted');
    }
}
