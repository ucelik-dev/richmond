<?php

namespace App\Http\Controllers\Frontend\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorDashboardController extends Controller
{
    function index() {
        $instructor = Auth::user();

        // Get groups assigned to this instructor
        $groups = $instructor->groups; // assuming `groups()` relationship exists in User model
        $groupCount = $groups->count();

        // Get unique student IDs through enrollments in instructor's groups
        $studentCount = Enrollment::whereIn('group_id', $groups->pluck('id'))
            ->distinct('user_id')
            ->count('user_id');
        
        $submissionsEvaluatedCount = AssignmentSubmission::where('assessor_id', $instructor->id)->whereNot('grade', 'pending')->count('id');
        $submissionsPendingCount = AssignmentSubmission::where(['assessor_id' => $instructor->id, 'grade' => 'pending'])->count('id');

        return view('frontend.instructor.dashboard', compact('groupCount', 'studentCount','submissionsEvaluatedCount','submissionsPendingCount'));
    }
}
