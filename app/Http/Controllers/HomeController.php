<?php

namespace App\Http\Controllers;

use App\Models\Student;

class HomeController extends Controller
{
    public function home()
    {
        $students = Student::select('it', 'full_name')->get()->sortBy('it');

        return view('home', compact('students'));
    }
}
