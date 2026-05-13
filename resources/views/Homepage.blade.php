<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Organization System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg px-5 py-3">
        <div class="container-fluid">

            <a class="navbar-brand fw-bold logo" href="#">
                SOS
            </a>

            <!-- Hamburger -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="#">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link nav-text" href="#">Organization</a>
                    </li>
                </ul>

            </div>

            <div>
                <a href="/login" class="btn login-btn me-2">Login</a>
                <a href="/register" class="btn signup-btn">Sign Up</a>
            </div>

        </div>
    </nav>

    <!-- Hero -->
    <section class="hero text-center">

        <h1 class="hero-title">
            Student Organization System
        </h1>

        <p class="hero-text">
            Connect with campus organizations, discover opportunities,
            and build your future together.
        </p>

        <div class="mt-4">
            <a href="/register" class="btn join-btn me-3">
                Join Now
            </a>

            <a href="#" class="btn explore-btn">
                Explore
            </a>
        </div>

        <div class="mt-5">
            <img src="{{ asset('student.png') }}" class="hero-image" alt="Student">
        </div>

    </section>

    <!-- Footer -->
    <footer class="text-center mt-5 mb-3 footer-text">
        © 2026 StudentOrg. All rights reserved.
    </footer>

  

</body>
</html>