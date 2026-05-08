<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold text-light" href="{{ url('/') }}">Student Organization System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('login.index') }}">SignIn</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('register.index') }}">Register</a></li>
            </ul>
        </div>
    </div>
</nav>