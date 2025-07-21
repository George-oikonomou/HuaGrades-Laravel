<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'home']);
Route::get('/students/{student}/grades', [StudentController::class, 'grades']);
