<?php

use App\Livewire\AiInsightsManager;
use App\Livewire\CategoryManager;
use App\Livewire\Dashboard;
use App\Livewire\GoalManager;
use App\Livewire\RecurringTransactionManager;
use App\Livewire\ReportManager;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\TransactionManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('teste', function () {
    return view('teste');
})->name('teste');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('categories', CategoryManager::class)->name('categories');
    Route::get('transactions', TransactionManager::class)->name('transactions');
    Route::get('goals', GoalManager::class)->name('goals');
    Route::get('recurring', RecurringTransactionManager::class)->name('recurring');
    Route::get('reports', ReportManager::class)->name('reports');
    Route::get('/insights', AiInsightsManager::class)
    ->name('insights');
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
