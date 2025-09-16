<?php

namespace App\Http\Controllers\Frontend\Pages;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CoursePageController extends Controller
{
    function index() {
        $courses = Course::with('level')->where('status','active')->paginate(9);
        return view('frontend.pages.course-page', compact('courses'));
    }

    function show(string $id) {
        $course = Course::with('level')->where('status','active')->where('id', $id)->firstOrFail();
        return view('frontend.pages.course-details-page', compact('course'));
    }

}
