<?php

namespace App\Services;

use App\Models\Course;

class CourseStatisticsService
{
    protected function baseQuery()
    {
        return Course::select('id', 'name', 'code', 'semester', 'type')
            ->with('grades')
            ->orderBy('semester')
            ->orderBy('type')
            ->orderBy('name');
    }


    public function getStatistics()
    {
        $courses = $this->baseQuery()->get();
        return $courses->map(fn($c) => $this->computeStatistics($c));
    }

    public function getCharts()
    {
        $courses = $this->baseQuery()->get();
        return $courses->map(fn($c) => $this->computeCharts($c));
    }

    protected function computeStatistics(Course $course): array
    {
        $distribution = array_fill(0, 11, 0);
        $total = $count = $passTotal = $passCount = 0;

        foreach ($course->grades as $grade) {
            $value = (int) $grade->grade;

            if ($value >= 0 && $value <= 10) {
                $distribution[$value]++;
            }

            $total += $value;
            $count++;

            if ($value >= 5) {
                $passTotal += $value;
                $passCount++;
            }
        }

        return [
            'name'         => $course->name,
            'code'         => $course->code,
            'semester'     => $course->semester,
            'type'         => $course->type,
            'average'      => $count ? round($total / $count, 2) : null,
            'pass_count'   => $passCount,
            'fail_count'   => $count - $passCount,
            'pass_average' => $passCount ? round($passTotal / $passCount, 2) : null,
            'distribution' => $distribution,
        ];
    }

    protected function computeCharts(Course $course): array
    {
        $distribution = array_fill(0, 11, 0);

        foreach ($course->grades as $grade) {
            $value = (int) $grade->grade;
            if ($value >= 0 && $value <= 10) {
                $distribution[$value]++;
            }
        }

        return [
            'name'         => $course->name,
            'code'         => $course->code,
            'semester'     => $course->semester,
            'type'         => $course->type,
            'distribution' => $distribution,
        ];
    }
}
