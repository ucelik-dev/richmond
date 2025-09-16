<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();

        // Get enrolled course IDs
        $enrolledCourseIds = $user->enrollments()->pluck('course_id');

        // Load only enrolled courses
        $courses = Course::with('modules','level','category')->whereIn('id', $enrolledCourseIds)->get();

        return view('frontend.student.course.index', compact('courses'));
    }

}
