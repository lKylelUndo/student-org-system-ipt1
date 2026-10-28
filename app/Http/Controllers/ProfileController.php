<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.setup', [
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:50'],
            'course' => ['required', 'string', 'max:255'],
            'year_level' => ['required', 'integer', 'between:1,4'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->update([
            ...$validated,
            'profile_completed_at' => now(),
        ]);

        return redirect()
            ->route('organizations.index')
            ->with('success', 'Profile completed! You can now explore organizations.');
    }
}