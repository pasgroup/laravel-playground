<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/favicon.ico', fn () => redirect(asset('favicon.svg'), 302))
    ->name('favicon');

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::post('/tasks/{task_uuid}/status', [TaskController::class, 'updateStatus'])->name('tasks.status.update');
Route::delete('/tasks/{task_uuid}', [TaskController::class, 'destroy'])->name('tasks.destroy');
