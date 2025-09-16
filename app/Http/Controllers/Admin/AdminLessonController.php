<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLessonCreateRequest;
use App\Http\Requests\Admin\AdminLessonUpdateRequest;
use App\Models\Lesson;
use App\Models\LessonModule;
use App\Models\Module;
use DB;
use Illuminate\Http\Request;

class AdminLessonController extends Controller
{

   public function index()
    {
        $lessons = Lesson::with('modules.courses')->paginate(25);
        return view ('admin.lesson.index', compact('lessons'));
    }

    public function create()
    {
        $modules = Module::all();
        return view ('admin.lesson.create', compact('modules'));
    }

    public function store(AdminLessonCreateRequest $request)
    {
        $lesson = new Lesson();
        $lesson->title = $request->title;
        $lesson->content = $request->content;
        $lesson->video_url = $request->video_url;
        $lesson->status = $request->status;
        $lesson->order = $request->order;
        $lesson->save();

        foreach ($request->modules as $moduleId) {
            DB::table('lesson_modules')->insert([
                'lesson_id' => $lesson->id,
                'module_id' => $moduleId
            ]);
        }

        notyf()->success('Created successfully!');
        return redirect()->route('admin.lesson.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $lesson = Lesson::with('modules')->findOrFail($id);
        $selectedModules = $lesson->modules->pluck('id')->toArray();

        return view('admin.lesson.edit', [
            'lesson' => $lesson,
            'modules' => Module::all(),
            'selectedModules' => $selectedModules,
        ]);
    }

    public function update(AdminLessonUpdateRequest $request, string $id)
    {
        $lesson = Lesson::findOrFail($id);

        $lesson->title = $request->title;
        $lesson->content = $request->content;
        $lesson->video_url = $request->video_url;
        $lesson->status = $request->status;
        $lesson->order = $request->order;
        $lesson->update();

        $lesson->modules()->sync($request->modules ?? []);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.lesson.index');
    }

    public function destroy(string $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);
            $lesson->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
