<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\GraduateDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminGraduateCreateRequest;
use App\Http\Requests\Admin\AdminGraduateUpdateRequest;
use App\Models\Graduate;
use App\Models\User;
use App\Models\UserStatus;
use App\Traits\FileUpload;
use Illuminate\Http\Request;

class AdminGraduateController extends Controller
{
    use FileUpload;

    public function index(GraduateDataTable $dataTable)
    {
        $graduates = Graduate::with(['user', 'course.level'])->latest('rc_graduation_date')->paginate(20);
        return $dataTable->render('admin.graduate.index', compact('graduates'));
    }

    public function create()
    {
        // 1) Find the id of the "graduated" status
        $graduatedStatusId = UserStatus::whereRaw('LOWER(name) = ?', ['graduated'])
            ->value('id');

        // If the status is missing, just return an empty list to avoid errors
        $students = collect();
        if ($graduatedStatusId) {
            // 2) Users with that status, who are students, and who don't have any graduates yet
            $students = User::query()
                ->where('user_status_id', $graduatedStatusId)
                ->whereHas('roles', fn($q) => $q->where('name', 'student'))
                ->doesntHave('graduates')           // no rows in graduates for this user
                ->orderBy('name')
                ->get();
        }

        // Empty instance for form binding if needed
        $graduate = new Graduate();

        $students = User::whereHas('roles', fn($q)=>$q->where('name','student'))
            ->where('user_status_id', $graduatedStatusId)
            ->doesntHave('graduates')
            ->with(['enrollments.course.level'])   // <-- needed
            ->orderBy('name')
            ->get();

        return view('admin.graduate.create', compact('students', 'graduate'));
    }

    public function store(AdminGraduateCreateRequest $request)
    {
        
        $user = User::with(['enrollments'])->findOrFail($request->user_id);

        // 1) Ensure selected course belongs to this student's enrollments
        $enrolled = $user->enrollments->contains(fn ($e) => (int)$e->course_id === (int)$request->course_id);
        if (!$enrolled) {
            notyf()->error('Selected course is not in the student’s enrollments!');
            return back();
        }

        // 2) Block duplicate graduation for same course
        $duplicate = Graduate::where('user_id', $user->id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($duplicate) {
            notyf()->error('A graduation for this course already exists for the selected student!');
            return back();
        }

        // 3) Prepare payload
        $payload = [
            'user_id'            => $user->id,
            'course_id'          => $request->course_id,
            'rc_graduation_date' => $request->rc_graduation_date ?: null,
            'top_up_date'        => $request->top_up_date ?: null,
            'university'         => $request->university ?: null,
            'program'            => $request->program ?: null,
            'study_mode'         => $request->study_mode ?: null,
            'program_entry_date' => $request->program_entry_date ?: null,
            'job_status'         => $request->filled('job_status') ? (int)$request->job_status : null,
            'job_title'          => $request->job_title ?: null,
            'job_start_date'     => $request->job_start_date ?: null,
            'note'               => $request->note ?: null,
        ];

        // 4) Handle diploma file (same style as your other uploads)
        if ($request->hasFile('diploma_file') && $request->file('diploma_file')->isValid()) {
            $prefix    = 'diploma';
            $unique    = $prefix . '_' . uniqid();
            $extension = $request->file('diploma_file')->getClientOriginalExtension();
            $filename  = $unique . '.' . $extension;

            // store under public/uploads/graduations
            $request->file('diploma_file')->move(public_path('uploads/graduations'), $filename);

            // relative path for asset()
            $payload['diploma_file'] = 'uploads/graduations/' . $filename;
        }

        // 5) Create graduate
        Graduate::create($payload);

        notyf()->success('Graduate record created successfully!');
        return redirect()->route('admin.graduate.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $graduate = Graduate::findOrFail($id);
        $graduate->load(['user.enrollments.course.level', 'course.level']);
        return view('admin.graduate.edit', compact('graduate'));
    }

    public function update(AdminGraduateUpdateRequest $request, Graduate $graduate)
    {

        $userId   = (int) $request->user_id;          // from hidden input
        $courseId = (int) $request->course_id;

        // Safety: ensure we’re updating for the same student (you keep it locked in the form)
        if ($graduate->user_id !== $userId) {
            notyf()->error('Invalid student for this graduation.!');
            return back();
        }

        // 1) Ensure course belongs to this student's enrollments
        $user = User::with('enrollments')->findOrFail($userId);
        $enrolled = $user->enrollments->contains(fn ($e) => (int) $e->course_id === $courseId);
        if (!$enrolled) {
            notyf()->error('Selected course is not in the student’s enrollments!');
            return back();
        }

        // 2) Block duplicate graduation (same user + course), excluding current record
        $duplicate = Graduate::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('id', '!=', $graduate->id)
            ->exists();

        if ($duplicate) {
            notyf()->error('A graduation for this course already exists for the selected student!');
            return back();
        }

        // 3) Build payload
        $payload = [
            'course_id'          => $courseId,
            'rc_graduation_date' => $request->rc_graduation_date ?: null,
            'top_up_date'        => $request->top_up_date ?: null,
            'university'         => $request->university ?: null,
            'program'            => $request->program ?: null,
            'study_mode'         => $request->study_mode ?: null,
            'program_entry_date' => $request->program_entry_date ?: null,
            'job_status'         => $request->filled('job_status') ? (int) $request->job_status : null,
            'job_title'          => $request->job_title ?: null,
            'job_start_date'     => $request->job_start_date ?: null,
            'note'               => $request->note ?: null,
        ];

        // 4) Handle diploma file replacement (only if a new file uploaded)
        if ($request->hasFile('diploma_file') && $request->file('diploma_file')->isValid()) {
            $prefix    = 'diploma';
            $unique    = $prefix . '_' . uniqid();
            $extension = $request->file('diploma_file')->getClientOriginalExtension();
            $filename  = $unique . '.' . $extension;

            // store under public/uploads/graduations
            $request->file('diploma_file')->move(public_path('uploads/graduations'), $filename);
            $payload['diploma_file'] = 'uploads/graduations/' . $filename;

            // delete old file if present
            if (!empty($graduate->diploma_file)) {
                if (method_exists($this, 'deleteFile')) {
                    $this->deleteFile($graduate->diploma_file);
                } else {
                    $full = public_path($graduate->diploma_file);
                    if (is_file($full)) @unlink($full);
                }
            }
        }

        // 5) Update row
        $graduate->update($payload);

        notyf()->success('Graduate record updated successfully!');
        return redirect()->route('admin.graduate.index');
    }

    public function destroy(string $id)
    {
        try {
            $graduate = Graduate::findOrFail($id);
            $graduate->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
