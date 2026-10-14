<html>

<head>
    <title>Register user id</title>
</head>

<body>
    <h1>Create Account</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif



    <form method="POST" action="/register">
        @csrf

        <label>Name: </label>
        <input type="text" name="name">

        <label>Email: </label>
        <input type="email" name="email">

        <label>Password: </label>
        <input type="password" name="password">
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        @error('password_confirmation')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Register</button>
    </form>
</body>

</html>
