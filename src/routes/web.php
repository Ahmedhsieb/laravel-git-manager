<?php

use Illuminate\Support\Facades\Route;
use Ahmedhsieb\GitManager\Http\Controllers\GitManagerController;

$prefix = config('git-manager.route_prefix', 'git-manager');
$middleware = array_merge(
    config('git-manager.middleware', ['web']),
    ['git-manager-auth']
);

// Authentication routes (without auth middleware)
Route::prefix($prefix)
    ->middleware(config('git-manager.middleware', ['web']))
    ->name('git-manager.')
    ->group(function () {
        Route::get('/login', [GitManagerController::class, 'showLogin'])->name('login');
        Route::post('/authenticate', [GitManagerController::class, 'authenticate'])->name('authenticate');
        Route::post('/logout', [GitManagerController::class, 'logout'])->name('logout');
    });

// Protected routes (with auth middleware)
Route::prefix($prefix)
    ->middleware($middleware)
    ->name('git-manager.')
    ->group(function () {

        // Main interface
        Route::get('/', [GitManagerController::class, 'index'])->name('index');

        // Repository Info
        Route::get('/status', [GitManagerController::class, 'status'])->name('status');
        Route::get('/current-branch', [GitManagerController::class, 'currentBranch'])->name('current-branch');
        Route::get('/branches', [GitManagerController::class, 'branches'])->name('branches');
        Route::get('/log', [GitManagerController::class, 'log'])->name('log');
        Route::get('/diff', [GitManagerController::class, 'diff'])->name('diff');

        // Git User
        Route::get('/user', [GitManagerController::class, 'getGitUser'])->name('user');
        Route::post('/user/config', [GitManagerController::class, 'configUser'])->name('user.config');

        // Basic Operations
        Route::post('/add', [GitManagerController::class, 'add'])->name('add');
        Route::post('/commit', [GitManagerController::class, 'commit'])->name('commit');
        Route::post('/push', [GitManagerController::class, 'push'])->name('push');
        Route::post('/pull', [GitManagerController::class, 'pull'])->name('pull');
        Route::post('/fetch', [GitManagerController::class, 'fetch'])->name('fetch');

        // Branch Management
        Route::post('/checkout', [GitManagerController::class, 'checkout'])->name('checkout');
        Route::post('/merge', [GitManagerController::class, 'merge'])->name('merge');
        Route::post('/branch/create', [GitManagerController::class, 'createBranch'])->name('branch.create');
        Route::delete('/branch/delete', [GitManagerController::class, 'deleteBranch'])->name('branch.delete');

        // Stash Operations
        Route::post('/stash', [GitManagerController::class, 'stash'])->name('stash');
        Route::post('/stash/pop', [GitManagerController::class, 'stashPop'])->name('stash.pop');
        Route::get('/stash/list', [GitManagerController::class, 'stashList'])->name('stash.list');

        // Reset & Full Workflow
        Route::post('/reset', [GitManagerController::class, 'reset'])->name('reset');
        Route::post('/full-push', [GitManagerController::class, 'fullPush'])->name('full-push');

        // Custom Commands
        Route::post('/custom', [GitManagerController::class, 'executeCustom'])->name('custom');
        Route::post('/artisan', [GitManagerController::class, 'executeArtisan'])->name('artisan');
    });
