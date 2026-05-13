<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
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

    <!-- LOGIN SECTION -->
    <div class="login-wrapper">

         <!-- LEFT IMAGE -->
        <div class="login-left">
            <img src="{{ asset('login.png') }}" alt="Students">
        </div>

        <!-- RIGHT LOGIN -->
        <div class="login-right">

            <div class="login-box">

                <h2 class="login-title">
                    Student Organization System
                </h2>

                <form action="/login" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label>Email</label>

                        <input 
                            type="email" 
                            name="email" 
                            class="form-control"
                            placeholder="jack@gmail.com"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label>Password</label>

                        <input 
                            type="password" 
                            name="password" 
                            class="form-control"
                            placeholder="************"
                            required
                        >
                    </div>

                    <button type="submit" class="btn-login">
                        Login
                    </button>

                    <p>
                        Don't have account?
                        <a href="/register">Register</a>
                    </p>

                </form>

            </div>

        </div>

    </div>

</body>
</html>