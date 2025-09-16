<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionFile;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAssignmentController extends Controller
{
    use FileUpload;
    
    function index() {
        $studentId = Auth::user()->id;

        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('user_id', $studentId)->pluck('course_id');

        // Group modules by course
        $modulesGroupedByCourse = [];

        $courses = Course::with(['modules' => function ($query) use ($studentId) {
            $query->with(['submissions' => function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            }]);
        }, 'modules.lessons', 'level'])
        ->whereIn('id', $enrolledCourseIds)
        ->get();

        foreach ($courses as $course) {
            $modulesGroupedByCourse[$course->id] = [
                'course' => $course,
                'modules' => $course->modules
            ];
        }

        return view ('frontend.student.assignment.index', compact('modulesGroupedByCourse'));
    }





    public function store(Request $request, Module $module)
    {

        $studentId = Auth::id();

        $request->validate([
            'files.*' => 'required|file|mimes:xls,xlsx,doc,docx,ppt,pptx,pdf,zip,rar|max:20480',
        ]);

        // Check if any existing submission is still pending
        $hasPending = AssignmentSubmission::where('student_id', $studentId)
            ->where('grade', 'pending')
            ->exists();

        if ($hasPending) {
            notyf()->error('You have a pending submission. Please wait until it is graded!');
            return redirect()->back();
        }

        // Get existing submissions count
        $latestSubmission  = AssignmentSubmission::where('student_id', $studentId)
            ->where('module_id', $module->id)
            ->latest()
            ->first();
        
        // Allow submission only if: - No submission exists yet - OR latest has extra_attempt = 1
        $canSubmit = !$latestSubmission || $latestSubmission->extra_attempt === 1;

        if (!$canSubmit) {
            notyf()->error('You cannot submit again unless an extra attempt is granted.!');
            return redirect()->back();
        }

        // Create new submission (reset extra_attempt to 0)
        $newSubmission = AssignmentSubmission::create([
            'student_id'    => $studentId,
            'module_id'     => $module->id,
            'grade'         => 'pending',
            'extra_attempt' => 0, // reset every time
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $path = $this->uploadFile($file, 'uploads/assignment-submissions', $studentId .'_' . $originalName);

                AssignmentSubmissionFile::create([
                    'assignment_submission_id' => $newSubmission->id,
                    'file' => $path, // relative path to file
                ]);
            }
        }

        notyf()->success('Files uploaded successfully!');
        return redirect()->back();

    }

}
