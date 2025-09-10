<?php

namespace App\Http\Controllers;

use App\Services\CourseStatisticsService;
use Illuminate\Http\Request;


class CourseStatisticsController extends Controller
{
    protected CourseStatisticsService $service;

    public function __construct(CourseStatisticsService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $statistics = $this->service->getStatistics();
        return view('statistics', compact('statistics'));
    }

    public function charts()
    {
        $charts = $this->service->getCharts();
        return view('charts', compact('charts'));
    }
}
