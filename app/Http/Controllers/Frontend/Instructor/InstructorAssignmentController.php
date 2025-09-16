<?php

namespace App\Http\Controllers\Frontend\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\User;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorAssignmentController extends Controller
{
    use FileUpload;
    
    public function index()
    {
        $instructorId = Auth::id();

        // Step 1: Get the group IDs managed by the current instructor
        $groupIds = Group::where('instructor_id', $instructorId)->pluck('id');

        // Step 2: Get student IDs where group_id belongs to the instructor's groups
        $studentIds = \App\Models\Enrollment::whereIn('group_id', $groupIds)
            ->pluck('user_id')->unique(); // optional, in case of duplicates

        $students = \App\Models\User::with(['enrollments.group'])
            ->whereIn('id', $studentIds)->get();

        // Step 3: Get enrolled course IDs for those students
        $enrolledCourseIds = Enrollment::whereIn('user_id', $studentIds)->pluck('course_id')->unique();

        // Step 4: Get courses with modules and relevant submissions
        $courses = Course::with([
            'modules' => function ($query) use ($studentIds) {
                $query->with([
                    'submissions' => function ($q) use ($studentIds) {
                        $q->whereIn('student_id', $studentIds)->with('student', 'files');
                    }
                ]);
            },
            'level'
        ])
        ->whereIn('id', $enrolledCourseIds)
        ->get();

        return view('frontend.instructor.assignment.index', compact('courses'));
    }

    public function edit(AssignmentSubmission $submission)
    {
        return view('frontend.instructor.assignment.edit', compact('submission'));
    }

    public function update(Request $request, $id)
    {
        $submission = AssignmentSubmission::findOrFail($id); 

        $request->validate([
            'grade' => 'required|in:pending,passed,merit,distinction,failed',
            'evaluated_at' => 'nullable|date_format:Y-m-d',
            'feedback_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'verification_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'plagiarism_report' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'feedback' => 'nullable|string|max:1000',
        ]);

        // Upload feedback file if provided
        if ($request->hasFile('feedback_file')) {
            $this->deleteFile($submission->feedback_file);
            $submission->feedback_file = $this->uploadFile($request->file('feedback_file'), 'uploads/assignment-submissions', $submission->student_id.'_feedback_file');
        }

        // Upload verification file if provided
        if ($request->hasFile('verification_file')) {
            $this->deleteFile($submission->verification_file);
            $submission->verification_file = $this->uploadFile($request->file('verification_file'), 'uploads/assignment-submissions', $submission->student_id.'_verification_file');
        }

        // Upload plagiarism report if provided
        if ($request->hasFile('plagiarism_report')) {
            $this->deleteFile($submission->plagiarism_report);
            $submission->plagiarism_report = $this->uploadFile($request->file('plagiarism_report'), 'uploads/assignment-submissions', $submission->student_id.'_plagiarism_report');
        }

        if ($request->filled('evaluated_at')) {
            $submission->evaluated_at = $request->evaluated_at;
        }

        // Update other fields
        $submission->assessor_id = Auth::user()->id;
        $submission->grade = $request->grade;
        $submission->feedback = $request->feedback;
        $submission->save();

        notyf()->success('Evaluation updated successfully!');
        return redirect()->route('instructor.assignment.index');

    }

    public function destroyEvaluation(AssignmentSubmission $submission)
    {
        // Fields to clear
        $fileFields = ['feedback_file', 'verification_file', 'plagiarism_report'];

        foreach ($fileFields as $field) {
            if ($submission->$field && file_exists(public_path($submission->$field))) {
                @unlink(public_path($submission->$field));
            }
            $submission->$field = null;
        }

        // Reset grade and evaluation date
        $submission->grade = 'pending';
        $submission->evaluated_at = null;
        $submission->save();

        notyf()->success('Evaluation deleted successfully!');
        return redirect()->route('instructor.assignment.index');
    }


}
