<!DOCTYPE html>
<html>

<head>
    <title>Edit Expense</title>
</head>

<body>
    <h1>Edit Expense</h1>

    <form method="POST" action="/expenses/{{ $expense->id }}">
        @csrf
        @method('PUT')

        <label>Date: </label>
        <input type="date" name="input_date" value="{{ old('input_date', $expense->input_date) }}">
        @error('input_date')
            {{ $message }}
        @enderror

        <br><br>

        <label>Description: </label>
        <input type="text" name="descriptions" value="{{ old('descriptions', $expense->descriptions) }}">
        @error('descriptions')
            {{ $message }}
        @enderror

        <br><br>

        <label>Amount: </label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}">
        @error('amount')
            {{ $message }}
        @enderror

        <br><br>

        <label>Type: </label>
        <select name="type">

            <!-- This is basically the same thing
            @if ($expense->type == 'income')
<option value="income" selected>Income</option>
                <option value="expense">Expense</option>
@endif
            @if ($expense->type == 'expense')
<option value="income">Income</option>
                <option value="expense" selected>Expense</option>
@endif
            -->

            <option value="income" @selected(old('type', $expense->type) == 'income')>Income</option>
            <option value="expense" @selected(old('type', $expense->type) == 'expense')>Expense</option>
        </select>
        @error('type')
            {{ $message }}
        @enderror

        <br><br>

        <label>Payment Type: </label>
        <select name="payment_type">
            <option value="cash" @selected(old('payment_type', $expense->payment_type) == 'cash')>Cash</option>
            <option value="online" @selected(old('payment_type', $expense->payment_type) == 'online')>Online</option>
        </select>
        @error('payment_type')
            {{ $message }}
        @enderror

        <br><br>

        <button type="submit">Update Expense</button>
    </form>
</body>

</html>
