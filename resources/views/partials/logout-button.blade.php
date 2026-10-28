@if (Route::has('logout'))
    <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="{{ $class ?? 'sos-btn-outline' }}">Logout</button>
    </form>
@else
    <a href="{{ url('/login') }}" class="{{ $class ?? 'sos-btn-outline' }}">Logout</a>
@endif