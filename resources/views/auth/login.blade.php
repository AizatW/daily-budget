<html>

<head>
    <title>Login page</title>
</head>

<body>
    <h1>Login</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="/login">
        @csrf

        <label>Email: </label>
        <input type="email" name="email">

        <label>Password: </label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

        <br>
    </form>

    <form method="GET" action="/register">
        @csrf

        <button type="submit">Register</button>
    </form>
</body>

</html>
