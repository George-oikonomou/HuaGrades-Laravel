<?php

use App\Http\Controllers\CourseStatisticsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/students/{student}/grades', [StudentController::class, 'grades'])->name('students.grades');
Route::get('/courses/grades', [CourseStatisticsController::class, 'index'])->name('courses.grades');
Route::get('/courses/charts', [CourseStatisticsController::class,'charts'])->name('courses.charts');
