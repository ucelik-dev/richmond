<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\StudentDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStudentCreateRequest;
use App\Http\Requests\Admin\AdminStudentUpdateRequest;
use App\Mail\StudentAccountCreatedMail;
use App\Mail\StudentEnrolledMail;
use App\Models\AwardingBody;
use App\Models\Batch;
use App\Models\College;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\DocumentCategory;
use App\Models\EmailLog;
use App\Models\Group;
use App\Models\Role;
use App\Models\SocialPlatform;
use App\Models\User;
use App\Models\UserStatus;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class AdminStudentController extends Controller
{
    use FileUpload;

    public function index(StudentDataTable $dataTable)
    {
        $students = User::with([
            'enrollments.course',
            'enrollments.batch',
            'enrollments.group',
            'userStatus',
            'awardingBodyRegistrations',
            'payments.installments',
            'country'
        ])
        ->whereHas('mainRoleRelation', fn($q) => $q->where('name', 'student'))
        ->orderBy('name')
        ->get();
        
        return $dataTable->render('admin.student.index', compact('students'));
    }

    public function create()
    {
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $courses = Course::all();
        $roles = Role::all();
        $colleges = College::where('status', 1)->get();
        $awardingBodies = AwardingBody::where('status', 1)->get();
        
        $salesUsers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'sales'))
            ->where('account_status', 1)
            ->orderBy('name')
            ->get();

        $agentUsers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'agent'))
            ->where('account_status', 1)
            ->orderBy('company', 'ASC')
            ->get();

        $managerUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'manager'))
            ->where('account_status', 1)
            ->orderBy('company', 'ASC')
            ->get();

        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $groups = Group::where('status', 1)->orderBy('name', 'ASC')->get();
        $batches = Batch::where('status', 1)->orderBy('name', 'ASC')->get();
        $courseLevels = CourseLevel::where('status', 1)->whereNot('id', 8)->orderBy('name', 'ASC')->get();
        $documentCategories = DocumentCategory::where(['role_id' => 2, 'status' => 1])->get(); 
        $socialPlatforms = SocialPlatform::where('status', 1)->get();

        return view('admin.student.create', compact('courses','roles','colleges','awardingBodies','salesUsers','agentUsers','managerUsers','userStatuses','groups','batches','documentCategories','socialPlatforms','countries','courseLevels'));
    }


    public function store(AdminStudentCreateRequest $request)
    {
        $student = new User();

        // Upload profile image if present
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $student->image = $imagePath;
        }

        // Assign basic fields
        $student->college_id = $request->college_id;
        $student->name = $request->name;
        $student->gender = strtolower($request->gender);
        $student->phone = $request->phone;
        $student->email = $request->email;
        $student->contact_email = $request->contact_email ?? $request->email;
        $student->dob = $request->dob;
        $student->education_status = $request->education_status;
        $student->post_code = $request->post_code;
        $student->city = $request->city;
        $student->country_id = $request->country_id;
        $student->address = $request->address;
        $student->bio = $request->bio;
        $student->account_status = $request->account_status;
        $student->user_status_id = $request->user_status_id;

        $student->sales_person_id = $request->sales_person_id;
        $student->agent_id = $request->agent_id;
        $student->manager_id = $request->manager_id;
        $student->reference = $request->reference;


        // Set password (required for create)
        $student->password = bcrypt($request->password);
        $student->save();

        
        // Send email notification
        //if (config('mail_queue.is_queue')) {
        //    Mail::to($student->contact_email)->queue(new StudentCreateMail($student));
        //} else {

            try {
                Mail::to($student->contact_email)->send(new StudentAccountCreatedMail($student));
                notyf()->success('Account Create email sent.');
            } catch (\Throwable $e) {
                // Mark last row for this user + mailable as failed
                EmailLog::where('user_id', $student->id)
                    ->where('mailable', StudentAccountCreatedMail::class)
                    ->latest()->first()?->update([
                        'status' => 'failed',
                        'error'  => $e->getMessage(),
                    ]);

                notyf()->error('Account Create email could not be sent.');
            }
        
        //}

        // Assign roles and sync permissions using the helper method
        // Assuming role 2 is always the 'student' role
        $this->attachRolesAndPermissions($student, [2], 2);



        ///////////////////////////////////////////////////////////
        //////////////////// COURSE ENROLLMENTS ///////////////////
        ///////////////////////////////////////////////////////////

        $enrolledCourses = [];

        if ($request->filled('enrollments')) {
            foreach ($request->enrollments as $enrollment) {
                if (!empty($enrollment['course_id'])) {
                    $student->enrollments()->create([
                        'course_id' => $enrollment['course_id'],
                        'group_id' => $enrollment['group_id'] ?? null,
                        'batch_id' => $enrollment['batch_id'] ?? null,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]);

                    $enrolledCourses[] = $enrollment['course_id'];
                }
            }
        }

        // Send enrollment email if any courses were added
        if (count($enrolledCourses)) {

            try {
                Mail::to($student->contact_email)->send(new StudentEnrolledMail($student));
                notyf()->success('Course Enrollment email sent.');
            } catch (\Throwable $e) {
                EmailLog::where('user_id', $student->id)
                    ->where('mailable', StudentEnrolledMail::class)
                    ->latest()->first()?->update([
                        'status' => 'failed',
                        'error'  => $e->getMessage(),
                    ]);

                notyf()->error('Course Enrollment email could not be sent.');
            }

        }

        ///////////////////////////////////////////////////////////
        //////////////////////// DOCUMENTS ////////////////////////
        ///////////////////////////////////////////////////////////

        if ($request->has('documents')) {
            foreach ($request->documents as $document) {
                if (isset($document['file']) && $document['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $categoryId = $document['category_id'];
                    $category = DocumentCategory::find($categoryId);

                    if ($category) {
                        $prefix = \Str::slug($category->name);
                        $uniqueName = $prefix . '_' . uniqid();
                        $extension = $document['file']->getClientOriginalExtension();
                        $filename = $uniqueName . '.' . $extension;

                        $document['file']->move(public_path('uploads/user-documents'), $filename);
                        $filePath = 'uploads/user-documents/' . $filename;

                        $student->documents()->create([
                            'category_id' => $categoryId,
                            'path' => $filePath,
                            'date' => $document['date'] ?? null,
                        ]);
                    }
                }
            }
        }

        ///////////////////////////////////////////////////////////
        ///////////////////// SOCIAL PLATFORMS ////////////////////
        ///////////////////////////////////////////////////////////

        if ($request->has('social_accounts')) {
            $submittedSocialAccountIds = [];

            foreach ($request->social_accounts as $account) {
                // Skip if platform or link missing
                if (empty($account['platform_id']) || empty($account['link'])) {
                    continue;
                }

                if (!empty($account['id'])) {
                    // Check if existing and update only if changed
                    $existing = $student->socialAccounts()->where('id', $account['id'])->first();
                    if ($existing) {
                        if (
                            $existing->social_platform_id != $account['platform_id'] ||
                            $existing->link !== $account['link']
                        ) {
                            $existing->update([
                                'social_platform_id' => $account['platform_id'],
                                'link' => $account['link'],
                            ]);
                        }
                        $submittedSocialAccountIds[] = $existing->id;
                    }
                } else {
                    // Create new account
                    $new = $student->socialAccounts()->create([
                        'social_platform_id' => $account['platform_id'],
                        'link' => $account['link'],
                    ]);
                    $submittedSocialAccountIds[] = $new->id;
                }
            }

            // Delete accounts that were removed in the form
            $student->socialAccounts()->whereNotIn('id', $submittedSocialAccountIds)->delete();

        } else {
            // No accounts submitted — delete all
            $student->socialAccounts()->delete();
        }

        ///////////////////////////////////////////////////////////
        //////////////////////// USER NOTES ///////////////////////
        ///////////////////////////////////////////////////////////

        if ($request->has('user_notes')) {
            foreach ($request->user_notes as $noteInput) {
                if (!empty($noteInput['note'])) {
                    $student->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                }
            }
        }

        notyf()->success('Student created successfully!');
        return redirect()->route('admin.student.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $student = User::with([
            'roles' => function ($q) {
                $q->withPivot('is_main');
            },
            'enrollments.course',
            'enrollments.batch',
            'enrollments.group',
            'socialAccounts',
            'graduates',
        ])->findOrFail($id);

        $countries = Country::where('status', 1)->orderBy('name')->get();

        $colleges = College::where('status', 1)->get();
        $awardingBodies = AwardingBody::where('status', 1)->get();

        $salesUsers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'sales'))
            ->where('account_status', 1)
            ->orderBy('name')
            ->get();

        $agentUsers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'agent'))
            ->where('account_status', 1)
            ->orderBy('company', 'ASC')
            ->get();

        $managerUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'manager'))
            ->where('account_status', 1)
            ->orderBy('company', 'ASC')
            ->get();

        $enrolledCourseIds = $student->enrollments->pluck('course_id')->toArray();
        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $courses = Course::all(); 
        $courseLevels = CourseLevel::where('status', 1)->whereNot('id', 8)->orderBy('name', 'ASC')->get();
        $groups = Group::where('status', 1)->orderBy('name', 'ASC')->get();
        $batches = Batch::where('status', 1)->orderBy('name', 'ASC')->get();
        $documentCategories = DocumentCategory::where(['role_id' => 2, 'status' => 1])->get(); 
        $socialPlatforms = SocialPlatform::where('status', 1)->get();

        return view('admin.student.edit', [
            'student' => $student,
            'roles' => Role::all(),
            'colleges' => $colleges,
            'awardingBodies' => $awardingBodies,
            'salesUsers' => $salesUsers,
            'agentUsers' => $agentUsers,
            'managerUsers' => $managerUsers,
            'enrolledCourseIds' => $enrolledCourseIds,
            'courses' => $courses,
            'courseLevels' => $courseLevels,
            'userStatuses' => $userStatuses,
            'groups' => $groups,
            'batches' => $batches,
            'documentCategories' => $documentCategories,
            'socialPlatforms' => $socialPlatforms,
            'countries' => $countries,
        ]);
    }

    public function update(AdminStudentUpdateRequest $request, User $student)
    {
        // Handle profile image (still in users table)
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $this->deleteFile($student->image);
            $student->image = $imagePath;
        }





        ///////////////////////////////////////////////////////////
        //////////////////////// DOCUMENTS ////////////////////////
        ///////////////////////////////////////////////////////////

        if ($request->filled('deleted_documents')) {
            $deletedIds = explode(',', $request->deleted_documents);
            foreach ($deletedIds as $docId) {
                $doc = $student->documents()->find($docId);
                if ($doc) {
                    $this->deleteFile($doc->path); // optional: delete file from disk
                    $doc->delete();
                }
            }
        }

        // Handle newly uploaded documents (from dynamic table)
        if ($request->has('documents')) {
            foreach ($request->documents as $document) {
                if (isset($document['file']) && $document['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $categoryId = $document['category_id'];
                    $category = DocumentCategory::find($categoryId);

                    if ($category) {
                        $prefix = \Str::slug($category->name); // e.g., "id_card"
                        $uniqueName = $prefix . '_' . uniqid();
                        $extension = $document['file']->getClientOriginalExtension();
                        $filename = $uniqueName . '.' . $extension;

                        // Store file under public/uploads/user-documents
                        $document['file']->move(public_path('uploads/user-documents'), $filename);

                        // Save relative path (used with asset())
                        $filePath = 'uploads/user-documents/' . $filename;

                        $student->documents()->create([
                            'category_id' => $categoryId,
                            'path' => $filePath,
                            'date' => !empty($document['date']) ? $document['date'] : null,
                        ]);
                    }
                }
            }
        }






        ///////////////////////////////////////////////////////////
        //////////////////////// USER NOTES ///////////////////////
        ///////////////////////////////////////////////////////////

        if ($request->has('user_notes')) {
            $submittedNoteIds = [];

            foreach ($request->user_notes as $noteInput) {
                // Skip empty notes
                if (empty($noteInput['note'])) {
                    continue;
                }

                if (!empty($noteInput['id'])) {
                    // Update existing note
                    $existing = $student->userNotes()->where('id', $noteInput['id'])->first();
                    if ($existing) {
                        $existing->update([
                            'note' => $noteInput['note'],
                        ]);
                        $submittedNoteIds[] = $existing->id;
                    }
                } else {
                    // Create new note
                    $new = $student->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                    $submittedNoteIds[] = $new->id;
                }
            }

            // Delete removed notes
            $student->userNotes()->whereNotIn('id', $submittedNoteIds)->delete();
        } else {
            // No notes submitted; delete all
            $student->userNotes()->delete();
        }


        // Update main user fields
        $student->college_id = $request->college_id;
        $student->name = $request->name;
        $student->gender = strtolower($request->gender);
        $student->phone = $request->phone;
        $student->email = $request->email;
        $student->contact_email = $request->contact_email ?? $request->email;
        $student->dob = $request->dob;
        $student->education_status = $request->education_status;
        $student->post_code = $request->post_code;
        $student->city = $request->city;
        $student->country_id = $request->country_id;
        $student->address = $request->address;
        $student->bio = $request->bio;
        $student->account_status = $request->account_status;
        $student->user_status_id = $request->user_status_id;

        // Registration & commission info
        $student->sales_person_id = $request->sales_person_id;
        $student->agent_id = $request->agent_id;
        $student->manager_id = $request->manager_id;
        $student->reference = $request->reference;

        // Password
        if ($request->filled('password')) {
            $student->password = bcrypt($request->password);
        }

        $student->save();



        // ✅ NEW: Apply the same logic for roles and permissions as the store method
        // Assuming role 2 is always the 'student' role
        $this->attachRolesAndPermissions($student, [2], 2);






        ///////////////////////////////////////////////////////////
        /////////////// AWARDING BODY REGISTRATIONS ///////////////
        ///////////////////////////////////////////////////////////

        $existingRegistrationIds = $student->awardingBodyRegistrations()->pluck('id')->toArray();
        $incomingRegistrationIds = [];

        if ($request->filled('registrations')) {
            foreach ($request->registrations as $registration) {
                // Skip if all fields are empty
                if (collect($registration)->filter()->isEmpty()) {
                    continue;
                }

                // Ensure required fields are present before update/create
                $hasRequiredFields = isset(
                    $registration['course_id'],
                    $registration['awarding_body_id'],
                    $registration['awarding_body_registration_level_id'],
                    $registration['awarding_body_registration_number'],
                    $registration['awarding_body_registration_date']
                );

                if (!empty($registration['id']) && $hasRequiredFields) {
                    // Update existing
                    $existing = $student->awardingBodyRegistrations()
                        ->where('id', $registration['id'])->first();

                    if ($existing) {
                        $existing->update([
                            'course_id' => $registration['course_id'],
                            'awarding_body_id' => $registration['awarding_body_id'],
                            'awarding_body_registration_level_id' => $registration['awarding_body_registration_level_id'],
                            'awarding_body_registration_number' => $registration['awarding_body_registration_number'],
                            'awarding_body_registration_date' => $registration['awarding_body_registration_date'],
                        ]);
                        $incomingRegistrationIds[] = $registration['id'];
                    }

                } elseif ($hasRequiredFields) {
                    // Create new
                    $new = $student->awardingBodyRegistrations()->create([
                        'user_id' => $student->id,
                        'course_id' => $registration['course_id'],
                        'awarding_body_id' => $registration['awarding_body_id'],
                        'awarding_body_registration_level_id' => $registration['awarding_body_registration_level_id'],
                        'awarding_body_registration_number' => $registration['awarding_body_registration_number'],
                        'awarding_body_registration_date' => $registration['awarding_body_registration_date'],
                    ]);
                    $incomingRegistrationIds[] = $new->id;
                }
            }
        }

        // Delete removed registrations
        $toDelete = array_diff($existingRegistrationIds, $incomingRegistrationIds);
        $student->awardingBodyRegistrations()->whereIn('id', $toDelete)->delete();






        ///////////////////////////////////////////////////////////
        //////////////////////// GRADUATES ////////////////////////
        ///////////////////////////////////////////////////////////

        if ($request->filled('deleted_graduations')) {
            $deletedIds = array_filter(explode(',', $request->deleted_graduations));
            foreach ($deletedIds as $gradId) {
                $grad = $student->graduates()->find($gradId);
                if ($grad) {
                    // delete file from disk if you have a helper
                    if (!empty($grad->diploma_file)) {
                        // using your helper if it accepts relative public path:
                        $this->deleteFile($grad->diploma_file);
                        // or unlink(public_path($grad->diploma_file));
                    }
                    $grad->delete();
                }
            }
        }



        $existingGraduationIds = $student->graduates()->pluck('id')->toArray();
        $incomingGraduationIds = [];

        if ($request->filled('graduations')) {
            foreach ($request->graduations as $row) {
                // Skip totally empty rows (ignore the file key for emptiness check)
                if (collect($row)->except(['diploma_file', 'id'])->filter()->isEmpty()) {
                    continue;
                }

                
                // Build payload
                $payload = [
                    'course_id'           => $row['course_id'],
                    'rc_graduation_date'  => $row['rc_graduation_date'] ?? null,
                    'top_up_date'         => $row['top_up_date']        ?? null,
                    'university'          => $row['university']         ?? null,
                    'program'             => $row['program']            ?? null,
                    'study_mode'          => $row['study_mode']         ?? null,
                    'program_entry_date'  => $row['program_entry_date'] ?? null,
                    'job_status'          => array_key_exists('job_status', $row) ? (bool)$row['job_status'] : 0,
                    'job_title'           => $row['job_title']          ?? null,
                    'job_start_date'      => $row['job_start_date']     ?? null,
                    'note'                => $row['note']               ?? null,
                ];

                // If a new diploma file is uploaded, save it like your Documents code
                if (!empty($row['diploma_file']) && $row['diploma_file'] instanceof \Illuminate\Http\UploadedFile) {
                    // Build filename: e.g., diploma_<uniqid>.pdf
                    $prefix    = 'diploma';
                    $unique    = $prefix . '_' . uniqid();
                    $extension = $row['diploma_file']->getClientOriginalExtension();
                    $filename  = $unique . '.' . $extension;

                    // Store under public/uploads/graduations
                    $row['diploma_file']->move(public_path('uploads/graduations'), $filename);

                    // Save relative path for asset()
                    $payload['diploma_file'] = 'uploads/graduations/' . $filename;
                }

                $newCourseId = $payload['course_id'] ?? null;

                        if (!empty($row['id'])) {
                            // UPDATE
                            $existing = $student->graduates()->where('id', $row['id'])->first();
                            if ($existing) {
                                // If course_id is being set/changed, ensure no other graduate row for this user has the same course_id
                                if (!empty($newCourseId)) {
                                    $duplicate = $student->graduates()
                                        ->where('course_id', $newCourseId)
                                        ->where('id', '!=', $existing->id)
                                        ->exists();

                                    if ($duplicate) {
                                        return back()
                                            ->withErrors(['graduations' => 'A graduation for the selected course already exists for this student.'])
                                            ->withInput();
                                    }
                                }

                                // If replacing file, delete the old file
                                if (!empty($payload['diploma_file']) && !empty($existing->diploma_file)) {
                                    $this->deleteFile($existing->diploma_file);
                                }

                                $existing->update($payload);
                                $incomingGraduationIds[] = $existing->id;
                            }
                        } else {
                            // CREATE
                            if (!empty($newCourseId)) {
                                $duplicate = $student->graduates()
                                    ->where('course_id', $newCourseId)
                                    ->exists();

                                if ($duplicate) {
                                    return back()
                                        ->withErrors(['graduations' => 'A graduation for the selected course already exists for this student.'])
                                        ->withInput();
                                }
                            }

                            $new = $student->graduates()->create(array_merge($payload, [
                                'user_id' => $student->id,
                            ]));
                            $incomingGraduationIds[] = $new->id;
                        }

            }
        }

        // Optional safety: if you ALSO use diff-based deletion:
        $toDelete = array_diff($existingGraduationIds, $incomingGraduationIds);
        if (!empty($toDelete)) {
            $student->graduates()->whereIn('id', $toDelete)->get()->each(function ($g) {
                if (!empty($g->diploma_file)) {
                    $this->deleteFile($g->diploma_file);
                }
            });
            $student->graduates()->whereIn('id', $toDelete)->delete();
        }











        ///////////////////////////////////////////////////////////
        //////////////////// COURSE ENROLLMENTS ///////////////////
        ///////////////////////////////////////////////////////////

        if ($request->filled('enrollments')) {
            $submittedCourseIds = collect($request->input('enrollments'))->pluck('course_id')->filter()->unique()->toArray();

            // Delete enrollments that are no longer present
            $student->enrollments()->whereNotIn('course_id', $submittedCourseIds)->delete();

            // Get existing course_ids
            $existingEnrollments = $student->enrollments()->get()->keyBy('course_id');

            $newCourseIds = []; // ✅ Initialize this to track newly created enrollments

            foreach ($request->enrollments as $enrollment) {
                if (empty($enrollment['course_id'])) {
                    continue;
                }

                $courseId = $enrollment['course_id'];
                $groupId = $enrollment['group_id'] ?? null;
                $batchId = $enrollment['batch_id'] ?? null;

                if ($existingEnrollments->has($courseId)) {
                    // Update existing enrollment
                    $existingEnrollments[$courseId]->update([
                        'group_id' => $groupId,
                        'batch_id' => $batchId,
                    ]);
                } else {
                    // Create new enrollment
                    $student->enrollments()->create([
                        'user_id' => $student->id,
                        'course_id' => $courseId,
                        'group_id' => $groupId,
                        'batch_id' => $batchId,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]);
                    $newCourseIds[] = $courseId; // ✅ Track newly added course
                }
            }

            // ✅ Send email if new courses added

            if (!empty($newCourseIds)) {

                try {
                    Mail::to($student->contact_email)->send(new StudentEnrolledMail($student));
                    notyf()->success('Course Enrollment email sent.');
                } catch (\Throwable $e) {
                    EmailLog::where('user_id', $student->id)
                        ->where('mailable', StudentEnrolledMail::class)
                        ->latest()->first()?->update([
                            'status' => 'failed',
                            'error'  => $e->getMessage(),
                        ]);

                    notyf()->error('Course Enrollment email could not be sent.');
                }

            }

        }




        notyf()->success('Student updated successfully!');

        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.student.index');
        }
        
    }


    public function destroy(string $id)
    {
        $student = User::findOrFail($id);

        // Block deletion if user has enrollments or payments
        if ($student->enrollments()->exists() || $student->payments()->exists()) {
            return response([
                'status' => 'error', 'message' => 'You cannot delete a student who has enrollments or payments!'], 500);
        }

        try {
            $this->deleteFile($student->image);
            $student->delete();
            return response(['status' => 'success', 'message' => 'Student deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }




    /**
     * Helper method to attach roles and sync permissions.
     */
    protected function attachRolesAndPermissions(User $user, array $roleIds, int $mainRoleId)
    {
        // First, sync the roles with the user, setting the main role.
        $pivotData = [];
        foreach ($roleIds as $roleId) {
            $pivotData[$roleId] = ['is_main' => ($roleId == $mainRoleId)];
        }
        $user->roles()->sync($pivotData);

        // Fetch all unique permission IDs from the selected roles.
        $permissions = Role::whereIn('id', $roleIds)
                            ->with('permissions')
                            ->get()
                            ->flatMap(fn ($role) => $role->permissions)
                            ->pluck('id')
                            ->unique();

        // Finally, sync these permissions directly to the user_permissions table.
        // This removes any old permissions and adds the new ones.
        $user->directPermissions()->sync($permissions);
    }
    

}
