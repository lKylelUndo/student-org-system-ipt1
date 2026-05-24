<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isAdmin()) {
            return redirect()->route('admin.organizations.index');
        }

        if ($user && ! $user->hasCompletedProfile() && ! $request->routeIs('profile.setup')) {
            return redirect()->route('profile.setup');
        }

        return $next($request);
    }
}