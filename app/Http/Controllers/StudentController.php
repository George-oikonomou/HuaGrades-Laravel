<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\GradeService;

class StudentController extends Controller
{
    protected GradeService $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function grades(Student $student)
    {
        $allGrades = $this->gradeService->getFormattedGrades($student);
        $averages  = $this->gradeService->getAverages($student);

        return response()->json([
            'grades' => $allGrades,
            'averages' => $averages,
        ]);
    }
}
