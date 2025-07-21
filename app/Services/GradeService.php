<?php

namespace App\Services;

use App\Models\Student;

class GradeService
{
    public function getFormattedGrades(Student $student): array
    {
        return $student->grades()
            ->select('grades.grade', 'grades.course_id')
            ->with(['course:id,name,semester'])
            ->get()
            ->map(function ($grade) {
                return [
                    'course' => $grade->course->name ?? 'Unknown',
                    'semester' => $grade->course->semester ?? null,
                    'grade' => $grade->grade,
                ];
            })
            ->toArray();
    }

    public function getAverages(Student $student): array
    {
        $averages = $student->grades()
            ->join('courses', 'grades.course_id', '=', 'courses.id')
            ->selectRaw("
                ROUND(AVG(CASE WHEN courses.semester IN (1,2) THEN grades.grade END), 2) as year_1,
                ROUND(AVG(CASE WHEN courses.semester IN (3,4) THEN grades.grade END), 2) as year_2,
                ROUND(AVG(CASE WHEN courses.semester IN (5,6) THEN grades.grade END), 2) as year_3,
                ROUND(AVG(grades.grade), 2) as total_average
            ")
            ->first();

        return [
            'year_1' => $averages->year_1,
            'year_2' => $averages->year_2,
            'year_3' => $averages->year_3,
            'total_average' => $averages->total_average,
        ];
    }
}
