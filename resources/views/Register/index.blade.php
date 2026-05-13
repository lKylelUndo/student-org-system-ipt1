<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar d-flex justify-content-between align-items-center">

        <div class="logo">
            SOS
        </div>

        <div class="d-flex gap-2">
            <a href="/login" class="btn login-btn">Login</a>
            <a href="/register" class="btn signup-btn">Sign Up</a>
        </div>

    </nav>

    <!-- REGISTER SECTION -->
    <div class="login-wrapper">

        <!-- LEFT IMAGE -->
        <div class="login-left">
            <img src="{{ asset('login.png') }}" alt="Students">
        </div>

        <!-- RIGHT REGISTER -->
        <div class="login-right">

            <div class="login-box">

                <h2 class="login-title">
                    Create Account
                </h2>

                <form action="/register" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label>Name</label>

                        <input 
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Enter your name"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label>Email</label>

                        <input 
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Enter your email"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label>Password</label>

                        <input 
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter your password"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label>Confirm Password</label>

                        <input 
                            type="password"
                            name="confirm_password"
                            class="form-control"
                            placeholder="Confirm your password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn-login">
                        Register
                    </button>

                    <p class="mt-3 text-center">
                        Already have an account?
                        <a href="/login">Login</a>
                    </p>

                </form>

            </div>

        </div>

    </div>

</body>
</html>