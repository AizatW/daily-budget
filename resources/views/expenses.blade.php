<!DOCTYPE html>
<html>

<head>
    <title>Daily Budget</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <div class="dashboard-container">

        <header class="dashboard-header">
            <div>
                <h1>Daily budget</h1>
            </div>

            <form method="post" action="/logout">
                @csrf

                <button type="submit" class="logout-btn">Logout</button>
            </form>

        </header>

        <main>

            <div class="summary-container">
                <div class="summary-card income-card">
                    <h3>Total Income</h3>
                    <p>RM {{ number_format($totalIncome, 2) }}</p>
                </div>

                <div class="summary-card expense-card">
                    <h3>Total Expense</h3>
                    <p>RM {{ number_format($totalExpense, 2) }}</p>
                </div>

                <div class="summary-card balance-card">
                    <h3>Balance</h3>
                    <p>RM {{ number_format($balance, 2) }}</p>
                </div>
            </div>

            <h2>Payment Method Breakdown</h2>

            <div class="payment-container">
                <div class="payment-card cash-card">
                    <h3>Cash</h3>

                    <p>Income: RM {{ number_format($cashIncome, 2) }}</p>
                    <p>Expense: RM {{ number_format($cashExpense, 2) }}</p>
                    <p>Balance: RM {{ number_format($cashBalance, 2) }}</p>
                </div>

                <div class="payment-card online-card">
                    <h3>Online</h3>

                    <p>Income: RM {{ number_format($onlineIncome, 2) }}</p>
                    <p>Expense: RM {{ number_format($onlineExpense, 2) }}</p>
                    <p>Balance: RM {{ number_format($onlineBalance, 2) }}</p>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-card">
                    <h2>Income vs Expenses - {{ $monthLabel }}</h2>

                    <canvas id="incomeExpenseChart"></canvas>
                </div>

                <div class="chart-card">
                    <h2>Daily Spending - {{ $monthLabel }}</h2>


                    <canvas id="dailySpendingChart"></canvas>
                </div>
            </div>


            <script>
                const totalIncome = {{ Illuminate\Support\Js::from($totalIncome) }};
                const totalExpense = {{ Illuminate\Support\Js::from($totalExpense) }};

                const dailyDates = {{ Illuminate\Support\Js::from($dailyTotals->keys()->values()) }};
                const dailyAmounts = {{ Illuminate\Support\Js::from($dailyTotals->values()) }};

                console.log(totalIncome);
                console.log(totalExpense);

                const chartCanvas = document.getElementById('incomeExpenseChart');
                const dailyCanvas = document.getElementById('dailySpendingChart');

                const chart = new Chart(chartCanvas, {
                    type: 'bar',

                    data: {
                        labels: ['income', 'Expenses'],

                        datasets: [{
                            label: 'Amount(RM)',
                            data: [totalIncome, totalExpense]
                        }]
                    }
                })

                const dailyChart = new Chart(dailyCanvas, {
                    type: 'line',

                    data: {
                        labels: dailyDates,

                        datasets: [{
                            label: 'Daily expenses(RM)',
                            data: dailyAmounts,
                            borderColor: 'blue',
                            tension: 0.2
                        }]
                    }
                })
            </script>


            <h1>My Expenses</h1>

            @if (session('success'))
                {{ session('success') }}
            @endif

            <p>This is my daily budget application</p>



            <form method="GET" action='/expenses' class="filter-form">

                <label for="search">Search: </label>
                <input type="text" id="search" name="search" placeholder="Search descriptionss"
                    value="{{ request('search') }}">

                <label>Type: </label>
                <select name="type">
                    <option value="all" @selected($type == 'all')>All</option>
                    <option value="income" @selected($type == 'income')>Income</option>
                    <option value="expense" @selected($type == 'expense')>Expense</option>
                </select>

                <label>Payment Type: </label>
                <select name="payment_type">
                    <option value="all" @selected($payment_type == 'all')>All</option>
                    <option value="cash" @selected($payment_type == 'cash')>Cash</option>
                    <option value="online" @selected($payment_type == 'online')>Online</option>
                </select>

                <label>Month: </label>
                <input type="month" name="month" value="{{ $month }}">
                <a
                    href="/expenses?month={{ $previousMonth }}&type={{ $type }}&payment_type={{ $payment_type }}&search={{ urlencode($search) }}">Previous</a>
                <a
                    href="/expenses?month={{ $nextMonth }}&type={{ $type }}&payment_type={{ $payment_type }}&search={{ urlencode($search) }}">Next</a>

                <button type="submit">Filter</button>
                <a href="/expenses?month={{ $month }}" class="reset-btn">Clear</a>
            </form>

            <section class="dashboard-section">
                <h2>Expense List - {{ $monthLabel }}</h2>

                <!--
    @forelse($expenses as $expense)
<p>
            {{ $expense->input_date }} |
            {{ $expense->descriptions }} |
            RM {{ number_format($expense->amount, 2) }} |
            {{ ucfirst($expense->type) }} |
            {{ ucfirst($expense->payment_type) }} |
            <a href="/expenses/{{ $expense->id }}/edit">Edit</a> |

        </p>

        <form method="POST" action='/expenses/{{ $expense->id }}'
            onsubmit="return confirm('Delete this transaction?')">
            @csrf
            @method('DELETE')
            <button type="submit">Delete</button>

@empty
    <p>No transactions found</p>
@endforelse
        </form>
    -->

                <div class="table-container">
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Payment type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->input_date }}</td>
                                    <td>{{ $expense->descriptions }}</td>
                                    <td class="{{ $expense->type == 'income' ? 'amount-income' : 'amount-expense' }}">
                                        RM
                                        {{ number_format($expense->amount, 2) }}</td>
                                    <td>
                                        <span
                                            class="type-badge {{ $expense->type == 'income' ? 'type-income' : 'type-expense' }}">
                                            {{ ucfirst($expense->type) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst($expense->payment_type) }}</td>

                                    <td>
                                        <div class="transaction-actions">
                                            <a href="/expenses/{{ $expense->id }}/edit" class="edit-btn">Edit</a>

                                            <form method="POST" action="/expenses/{{ $expense->id }}"
                                                onsubmit="return confirm('Delete this transaction?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="delete-btn">Delete</button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6">No Transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="pagination-container">
                    {{ $expenses->withQueryString()->links('custom') }}
                </div>

            </section>



            <!-- <label>Total Income: </label>
    <p>
        RM {{ number_format($totalIncome, 2) }}
    </p>
    <label>Total Expense: </label>
    <p>
        RM {{ number_format($totalExpense, 2) }}
    </p>
    <label>Average Expense: </label>
    <p>
        RM {{ number_format($averageExpense, 2) }}
    </p>

    <label>Balance: </label>
    <p>
        RM {{ number_format($balance, 2) }}
    </p> -->



            <h2>Add Expense</h2>

            <form method="POST" action="/expenses" class="expense-form" id="expenseForm">
                @csrf

                <div class="form-group">
                    <label for="input_date">Date</label>
                    <input type="date" id="input_date" name="input_date" value="{{ old('input_date') }}"
                        class="@error('input_date') input-error @enderror">
                    @error('input_date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descriptions">Description</label>
                    <input type="text" id="descriptions" name="descriptions" value="{{ old('descriptions') }}"
                        class="@error('descriptions') input-error @enderror">
                    @error('descriptions')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount') }}"
                        class="@error('amount') input-error @enderror">
                    @error('amount')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">Type</label>
                    <select name="type" id="type" class="@error('type') input-error @enderror">
                        <option value="" @selected(old('type') == null) disabled>---</option>
                        <option value="expense" @selected(old('type') == 'expense')>Expense</option>
                        <option value="income" @selected(old('type') == 'income')>Income</option>
                    </select>
                    @error('type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="payment_type">Payment type</label>
                    <select name="payment_type" id="payment_type"
                        class="@error('payment_type') input-error @enderror">
                        <option value="" @selected(old('payment_type') == null) disabled>---</option>
                        <option value="cash" @selected(old('payment_type') == 'cash')>Cash</option>
                        <option value="online" @selected(old('payment_type') == 'online')>Online/Card</option>
                    </select>
                    @error('payment_type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" id="addExpenseButton">Add Expense</button>

            </form>

            <label>Transactions: </label>
            <p>{{ $transactionCount }}</p>
            <label>Income Transactions: </label>
            <p>{{ $incomeCount }}</p>
            <label>Expense Transactions: </label>
            <p>{{ $expenseCount }}</p>

        </main>
    </div>

    <script>
        const expenseForm = document.getElementById("expenseForm");
        const addExpenseButton = document.getElementById('addExpenseButton');

        expenseForm.addEventListener('submit', function() {
            addExpenseButton.disabled = true;
            addExpenseButton.textContent = 'Saving...';
        });
    </script>
</body>

</html>
