<?php

namespace App\Services;

use App\Models\Course;

class CourseStatisticsService
{
    public function getStatisticsForAllCourses($courses)
    {
        return $courses->map(function ($course) {
            return $this->computeCourseStatistics($course);
        });
    }

    protected function computeCourseStatistics(Course $course): array
    {
        $distribution = array_fill(0, 11, 0);
        $total = 0;
        $count = 0;
        $passTotal = 0;
        $passCount = 0;

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
            'name' => $course->name,
            'code' => $course->code,
            'semester' => $course->semester,
            'type' => $course->type,
            'average' => $count ? round($total / $count, 2) : null,
            'pass_count' => $passCount,
            'fail_count' => $count - $passCount,
            'pass_average' => $passCount ? round($passTotal / $passCount, 2) : null,
            'distribution' => $distribution,
        ];
    }
}
