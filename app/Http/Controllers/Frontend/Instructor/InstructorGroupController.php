<?php

namespace App\Http\Controllers\Frontend\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CourseCreateRequest;
use App\Http\Requests\Frontend\CourseUpdateRequest;
use App\Models\Course;
use App\Models\Group;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorGroupController extends Controller
{
    use FileUpload;

    public function index()
    {
        $groups = Group::with(['students' => function ($query) {
            $query->select(
                'users.id as user_id',
                'enrollments.group_id',
                'users.account_status'
            );
        }])
        ->where('instructor_id', Auth::id())
        ->where('status', 1)
        ->get();

        // Group student counts by account_status for each group
        foreach ($groups as $group) {
            $activeCount = $group->students->where('account_status', 1)->count();
            $inactiveCount = $group->students->where('account_status', 0)->count();

            $group->active_student_count = $activeCount;
            $group->inactive_student_count = $inactiveCount;
        }

        return view('frontend.instructor.group.index', compact('groups'));
    }

   

    function store(Request $request) {

    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
       
    }

    public function update(Request $request, string $id)
    {

    }

    public function destroy(string $id)
    {
        try {
            
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }
}
