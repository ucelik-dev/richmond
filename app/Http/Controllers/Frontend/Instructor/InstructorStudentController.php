<?php

namespace App\Http\Controllers\Frontend\Instructor;

use App\DataTables\InstructorStudentsDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorStudentController extends Controller
{
    public function index(InstructorStudentsDataTable $dataTable){
        $instructorId = Auth::user()->id;

        // Get all students from all groups belonging to this instructor
        $students = User::with(['country','awardingBodyRegistrations'])
            ->whereHas('enrollments.group', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            })
            ->get();

        //return view('frontend.instructor.students.index', compact('students'));
        return $dataTable->render('frontend.instructor.students.index', compact('students'));
    }
}
