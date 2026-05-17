<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureProfileIsComplete;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile/setup', [ProfileController::class, 'show'])->name('profile.setup');
    Route::post('/profile/setup', [ProfileController::class, 'store']);

    Route::middleware(EnsureProfileIsComplete::class)->group(function () {
        Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
        Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
        Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
        Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');
        Route::get('/organizations/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit');
        Route::put('/organizations/{organization}', [OrganizationController::class, 'update'])->name('organizations.update');
        Route::delete('/organizations/{organization}', [OrganizationController::class, 'destroy'])->name('organizations.destroy');

        Route::post('/organizations/{organization}/join', [MembershipController::class, 'join'])->name('organizations.join');

        Route::get('/members', [MembershipController::class, 'index'])->name('members.index');
        Route::patch('/memberships/{membership}/role', [MembershipController::class, 'updateRole'])->name('memberships.role');
        Route::post('/memberships/{membership}/approve', [MembershipController::class, 'approve'])->name('memberships.approve');
        Route::post('/memberships/{membership}/decline', [MembershipController::class, 'decline'])->name('memberships.decline');
        Route::post('/memberships/{membership}/suspend', [MembershipController::class, 'suspend'])->name('memberships.suspend');
    });
});