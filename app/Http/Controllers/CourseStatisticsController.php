<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Services\CourseStatisticsService;
use App\Services\GradeService;

class CourseStatisticsController extends Controller
{
    protected CourseStatisticsService $service;

    public function __construct(CourseStatisticsService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $courses = Course::select('id', 'name', 'code', 'semester', 'type')
            ->with('grades')
            ->orderBy('semester')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $statistics = $this->service->getStatisticsForAllCourses($courses);
        return view('statistics', compact('statistics'));
    }
}
