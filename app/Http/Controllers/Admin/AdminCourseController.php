<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCourseCreateRequest;
use App\Http\Requests\Admin\AdminCourseUpdateRequest;
use App\Models\AwardingBody;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLevel;
use App\Models\Module;
use App\Traits\FileUpload;
use DB;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    use FileUpload;

    public function index()
    {
        $courses = Course::with('modules','level','category')->withCount('enrollments')->orderBy('title')->paginate(25);
        return view ('admin.course.index', compact('courses'));
    }

    public function create()
    {
        $statuses = ['draft','active','inactive'];
        $levels = CourseLevel::where('status', 1)->get();
        $categories = CourseCategory::where('status', 1)->get();
        $awardingBodies = AwardingBody::where('status', 1)->get();
        $modules = Module::all();
        return view ('admin.course.create', compact('statuses','modules','levels','categories','awardingBodies'));
    }

    function store(Request $request) {

        $thumbnailPath = $this->uploadFile($request->file('thumbnail'), 'uploads/courses', 'course_thumbnail');
        $logoPath = $this->uploadFile($request->file('logo'), 'uploads/courses', 'course_logo');
        $handbook_path = $this->uploadFile($request->file('handbook_file'), 'uploads/courses', 'handbook');
        $mapping_document_path = $this->uploadFile($request->file('mapping_document'), 'uploads/courses', 'mapping_document');
        $assignment_specification_path = $this->uploadFile($request->file('assignment_specification'), 'uploads/courses', 'assignment_specification');
        $curriculum_path = $this->uploadFile($request->file('curriculum'), 'uploads/courses', 'curriculum');

        $course = new Course();

        $course->title = $request->title;
        $course->extended_title = $request->extended_title;
        $course->code = $request->code;
        $course->credits = $request->credits;
        $course->level_id = $request->level_id;
        $course->category_id = $request->category_id;
        $course->awarding_body_id = $request->awarding_body_id;
        $course->price = $request->price;
        $course->discount = $request->discount;
        $course->description = $request->description;
        $course->overview = $request->overview;
        $course->overview_details = $request->overview_details;
        $course->learning_outcomes = $request->learning_outcomes;

        $course->thumbnail = $thumbnailPath;
        $course->logo = $logoPath;
        $course->handbook_file = $handbook_path;
        $course->mapping_document = $mapping_document_path;
        $course->assignment_specification = $assignment_specification_path;
        $course->curriculum = $curriculum_path;
        $course->demo_video_storage = $request->demo_video_storage;
        $course->demo_video_source = $request->demo_video_source;

        $course->status = $request->status;

        $course->completion_test = $request->completion_test ?? 0;
        $course->completion_certificate = $request->completion_certificate ?? 0;

        $course->save();

        if($request->modules){
            foreach ($request->modules as $moduleId) {
                DB::table('course_modules')->insert([
                    'course_id' => $course->id,
                    'module_id' => $moduleId
                ]);
            }
        }
        

        notyf()->success('Created successfully!');
        return redirect()->route('admin.course.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $statuses = ['draft','active','inactive'];
        $levels = CourseLevel::where('status', 1)->get();
        $categories = CourseCategory::where('status', 1)->get();
        $awardingBodies = AwardingBody::where('status', 1)->get();
        $course = Course::with('modules','level','category')->findOrFail($id);
        $selectedModules = $course->modules->pluck('id')->toArray();

        return view('admin.course.edit', [
            'course' => $course,
            'modules' => Module::all(),
            'selectedModules' => $selectedModules,
            'statuses' => $statuses,
            'levels' => $levels,
            'awardingBodies' => $awardingBodies,
            'categories' => $categories,
        ]);

    }

    public function update(AdminCourseUpdateRequest $request, string $id)
    {
        $course = Course::findOrFail($id);

        if($request->hasFile('thumbnail')){ 
            $thumbnailPath = $this->uploadFile($request->file('thumbnail'), 'uploads/courses', 'course_thumbnail');
            $course->thumbnail = $thumbnailPath;
        }
        if($request->hasFile('logo')){ 
            $logoPath = $this->uploadFile($request->file('logo'), 'uploads/courses', 'course_logo');
            $course->logo = $logoPath;
        }
        if($request->hasFile('handbook_file')){ 
            $handbook_path = $this->uploadFile($request->file('handbook_file'), 'uploads/courses', 'handbook');
            $course->handbook_file = $handbook_path;
        }
        if($request->hasFile('mapping_document')){ 
            $mapping_document_path = $this->uploadFile($request->file('mapping_document'), 'uploads/courses', 'mapping_document');
            $course->mapping_document = $mapping_document_path;
        }
        if($request->hasFile('assignment_specification')){ 
            $assignment_specification_path = $this->uploadFile($request->file('assignment_specification'), 'uploads/courses', 'assignment_specification');
            $course->assignment_specification = $assignment_specification_path;
        }
        if($request->hasFile('curriculum')){ 
            $curriculum_path = $this->uploadFile($request->file('curriculum'), 'uploads/courses', 'curriculum');
            $course->curriculum = $curriculum_path;
        }

        $course->title = $request->title;
        $course->extended_title = $request->extended_title;
        $course->code = $request->code;
        $course->credits = $request->credits;
        $course->level_id = $request->level_id;
        $course->category_id = $request->category_id;
        $course->awarding_body_id = $request->awarding_body_id;
        $course->price = $request->price;
        $course->discount = $request->discount;
        $course->description = $request->description;
        $course->overview = $request->overview;
        $course->overview_details = $request->overview_details;
        $course->learning_outcomes = $request->learning_outcomes;
        $course->status = $request->status;
        $course->show_in_select = $request->show_in_select;
        $course->completion_test = $request->completion_test ?? 0;
        $course->completion_certificate = $request->completion_certificate ?? 0;

        $course->demo_video_storage = $request->demo_video_storage;
        $course->demo_video_source = $request->demo_video_source;

        $course->update();

        $course->modules()->sync($request->modules ?? []);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.course.index');

    }

    public function destroy(string $id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }
}
