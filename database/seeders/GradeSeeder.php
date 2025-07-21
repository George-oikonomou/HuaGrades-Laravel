<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();

        // Group courses by semester and type
        $coursesBySemester = Course::all()->groupBy('semester');

        foreach ($students as $student) {
            // Semesters 1–4: All courses
            foreach (range(1, 4) as $semester) {
                if (!isset($coursesBySemester[$semester])) continue;

                foreach ($coursesBySemester[$semester] as $course) {
                    Grade::factory()->create([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ]);
                }
            }

            // Semester 5: All ΥΠΟΧΡΕΩΤΙΚΟ + 3 random ΕΠΙΛΟΓΗΣ
            if (isset($coursesBySemester[5])) {
                $sem5 = $coursesBySemester[5];
                $required = $sem5->where('type', 'ΥΠΟΧΡΕΩΤΙΚΟ');
                $selective = $sem5->where('type', 'ΕΠΙΛΟΓΗΣ')->shuffle()->take(3);

                foreach ($required->concat($selective) as $course) {
                    Grade::factory()->create([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ]);
                }
            }

            // Semester 6: All ΥΠΟΧΡΕΩΤΙΚΟ + 3 random ΕΠΙΛΟΓΗΣ
            if (isset($coursesBySemester[6])) {
                $sem6 = $coursesBySemester[6];
                $required = $sem6->where('type', 'ΥΠΟΧΡΕΩΤΙΚΟ');
                $selective = $sem6->where('type', 'ΕΠΙΛΟΓΗΣ')->shuffle()->take(3);

                foreach ($required->concat($selective) as $course) {
                    Grade::factory()->create([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ]);
                }
            }
        }
    }
}
