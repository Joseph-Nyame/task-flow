<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('projects', ProjectController::class);
Route::resource('tasks', TaskController::class);

Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');