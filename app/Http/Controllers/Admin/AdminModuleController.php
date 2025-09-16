<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminModuleCreateRequest;
use App\Http\Requests\Admin\AdminModuleUpdateRequest;
use App\Models\AwardingBody;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\Module;
use App\Traits\FileUpload;
use DB;
use Illuminate\Http\Request;

class AdminModuleController extends Controller
{
    use FileUpload;
    
    public function index()
    {
        $modules = Module::with(['lessons', 'courses'])
        ->select('modules.*')
        ->addSelect([
            'first_course_title' => function ($query) {
                $query->select('courses.title')
                    ->from('courses')
                    ->join('course_modules', 'courses.id', '=', 'course_modules.course_id')
                    ->whereColumn('course_modules.module_id', 'modules.id')
                    ->orderBy('courses.title')
                    ->limit(1);
            }
        ])
        ->orderBy('first_course_title') // Order by related course title
        ->orderBy('modules.level_id')      // Then by modules.order column
        ->orderBy('modules.order')
        ->paginate(100);
        return view ('admin.module.index', compact('modules'));
    }

    public function create()
    {
        $courses = Course::all();
        $levels = CourseLevel::where('status', 1)->get();
        $awardingBodies = AwardingBody::where('status', 1)->get();
        return view ('admin.module.create', compact('courses','levels','awardingBodies'));
    }

    public function store(AdminModuleCreateRequest $request)
    {
        $module = new Module();

        if ($request->hasFile('assignment_file')) {
            $assignmentPath = $this->uploadFile($request->file('assignment_file'), 'courses/modules', 'assignment');
            $module->assignment_file = $assignmentPath;
        }

        if ($request->hasFile('sample_assignment_file')) {
            $sampleAssignmentPath = $this->uploadFile($request->file('sample_assignment_file'), 'courses/modules', 'sample_assignment');
            $module->sample_assignment_file = $sampleAssignmentPath;
        }
        
        $module->title = $request->title;
        $module->level_id = $request->level_id;
        $module->awarding_body_id = $request->awarding_body_id;
        $module->description = $request->description;
        $module->overview = $request->overview;
        $module->learning_outcomes = $request->learning_outcomes;
        $module->video_url = $request->video_url;
        $module->status = $request->status;
        $module->order = $request->order;
        $module->save();

        if($request->courses){
            foreach ($request->courses as $courseId) {
                DB::table('course_modules')->insert([
                    'module_id' => $module->id,
                    'course_id' => $courseId
                ]);
            }
        }

        notyf()->success('Created successfully!');
        return redirect()->route('admin.module.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $module = Module::with('courses')->findOrFail($id);
        $selectedCourses = $module->courses->pluck('id')->toArray();
        $levels = CourseLevel::where('status', 1)->get();
        $awardingBodies = AwardingBody::where('status', 1)->get();

        return view('admin.module.edit', [
            'module' => $module,
            'courses' => Course::all(),
            'selectedCourses' => $selectedCourses,
            'levels' => $levels,
            'awardingBodies' => $awardingBodies,
        ]);

    }

    public function update(AdminModuleUpdateRequest $request, string $id)
    {
        $module = Module::findOrFail($id);

        if ($request->hasFile('assignment_file')) {
            $assignmentPath = $this->uploadFile($request->file('assignment_file'), 'courses/modules', 'assignment');
            $module->assignment_file = $assignmentPath;
        }

        if ($request->hasFile('sample_assignment_file')) {
            $sampleAssignmentPath = $this->uploadFile($request->file('sample_assignment_file'), 'courses/modules', 'sample_assignment');
            $module->sample_assignment_file = $sampleAssignmentPath;
        }

        $module->title = $request->title;
        $module->level_id = $request->level_id;
        $module->awarding_body_id = $request->awarding_body_id;
        $module->description = $request->description;
        $module->overview = $request->overview;
        $module->learning_outcomes = $request->learning_outcomes;
        $module->video_url = $request->video_url;
        $module->status = $request->status;
        $module->order = $request->order;
        $module->update();

        $module->courses()->sync($request->courses ?? []);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.module.index');
    }

    public function destroy(string $id)
    {
        try {
            $module = Module::findOrFail($id);
            $module->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
