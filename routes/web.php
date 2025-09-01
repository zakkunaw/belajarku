<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RoadmapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/today', function () {
    return view('today');
})->middleware(['auth', 'verified'])->name('today');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Study Sessions
    Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
    
    // Goals Management
    Route::get('/goals', [GoalController::class, 'index'])->name('goals');
    Route::get('/goals/{goal}', [GoalController::class, 'show'])->name('goals.show');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::patch('/goals/{goal}/status', [GoalController::class, 'updateStatus'])->name('goals.updateStatus');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');
    
    // Milestones Management
    Route::post('/goals/{goal}/milestones', [MilestoneController::class, 'store'])->name('milestones.store');
    Route::patch('/milestones/{milestone}/move', [MilestoneController::class, 'move'])->name('milestones.move');
    
    // Tasks Management
    Route::post('/milestones/{milestone}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    
    // Roadmap Management
    Route::get('/roadmap', [RoadmapController::class, 'index'])->name('roadmap');
    Route::post('/roadmap/import', [RoadmapController::class, 'import'])->name('roadmap.import');
});

require __DIR__.'/auth.php';
