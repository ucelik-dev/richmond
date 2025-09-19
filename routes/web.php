<?php

use App\Http\Controllers\Admin\AdminAgentController;
use App\Http\Controllers\Admin\AdminBulkEmailController;
use App\Http\Controllers\Admin\AdminCommissionController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEmailLogController;
use App\Http\Controllers\Admin\AdminExpenseController;
use App\Http\Controllers\Admin\AdminGraduateController;
use App\Http\Controllers\Admin\AdminImpersonationController;
use App\Http\Controllers\Admin\AdminIncomeController;
use App\Http\Controllers\Admin\AdminInstructorController;
use App\Http\Controllers\Admin\AdminLessonController;
use App\Http\Controllers\Admin\AdminManagerController;
use App\Http\Controllers\Admin\AdminModuleController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminRecruitmentController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\Setting\AdminCollegeController;
use App\Http\Controllers\Admin\Setting\AdminAwardingBodyController;
use App\Http\Controllers\Admin\Setting\AdminCountryController;
use App\Http\Controllers\Admin\Setting\AdminCourseCategoryController;
use App\Http\Controllers\Admin\Setting\AdminCourseLevelController;
use App\Http\Controllers\Admin\Setting\AdminDocumentCategoryController;
use App\Http\Controllers\Admin\Setting\AdminExpenseCategoryController;
use App\Http\Controllers\Admin\Setting\AdminIncomeCategoryController;
use App\Http\Controllers\Admin\Setting\AdminPaymentStatusController;
use App\Http\Controllers\Admin\Setting\AdminRecruitmentSourceController;
use App\Http\Controllers\Admin\Setting\AdminSocialPlatformController;
use App\Http\Controllers\Admin\Setting\AdminStudentBatchController;
use App\Http\Controllers\Admin\Setting\AdminStudentGroupController;
use App\Http\Controllers\Admin\Setting\AdminRecruitmentStatusController;
use App\Http\Controllers\Admin\Setting\AdminUserPermissionController;
use App\Http\Controllers\Admin\Setting\AdminUserRoleController;
use App\Http\Controllers\Admin\Setting\AdminUserStatusController;
use App\Http\Controllers\Frontend\Agent\AgentDashboardController;
use App\Http\Controllers\Frontend\Agent\AgentProfileController;
use App\Http\Controllers\Frontend\Agent\AgentRegistrationController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\Instructor\InstructorAssignmentController;
use App\Http\Controllers\Frontend\Student\StudentDashboardController;
use App\Http\Controllers\Frontend\Instructor\InstructorDashboardController;
use App\Http\Controllers\Frontend\Instructor\InstructorProfileController;
use App\Http\Controllers\Frontend\Instructor\InstructorGroupController;
use App\Http\Controllers\Frontend\Instructor\InstructorGroupShareController;
use App\Http\Controllers\Frontend\Instructor\InstructorStudentController;
use App\Http\Controllers\Frontend\Pages\CoursePageController;
use App\Http\Controllers\Frontend\Student\StudentAssignmentController;
use App\Http\Controllers\Frontend\Student\StudentCourseController;
use App\Http\Controllers\Frontend\Student\StudentDocumentController;
use App\Http\Controllers\Frontend\Student\StudentPaymentController;
use App\Http\Controllers\Frontend\Student\StudentProfileController;
use App\Http\Controllers\Frontend\Student\StudentEmailLogController;
use Illuminate\Support\Facades\Route;


/* Frontend Routes */

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/courses', [CoursePageController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [CoursePageController::class, 'show'])->name('courses.show');




/* Student Routes */

Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'student', 'as' => 'student.'], function(){

    Route::middleware(['auth', 'role:student'])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    });

    // Profile Routes Group with its specific permission check
    Route::group(['middleware' => ['permission:student_profile,view']], function(){
        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [StudentProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [StudentProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/update-social', [StudentProfileController::class, 'updateSocial'])->name('profile.update-social');
        Route::post('/profile/update-avatar', [StudentProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    });


    Route::get('/document', [StudentDocumentController::class, 'index'])->name('document')->middleware('permission:student_documents,view');
    Route::post('/document/update', [StudentDocumentController::class, 'updateDocument'])->name('document.update')->middleware('permission:student_documents,edit');

    Route::get('assignment', [StudentAssignmentController::class, 'index'])->name('assignment.index')->middleware('permission:student_assignments,view');
    Route::get('assignment/{module}/create', [StudentAssignmentController::class, 'create'])->name('assignment.create')->middleware('permission:student_assignments,edit');
    Route::post('assignment/{module}', [StudentAssignmentController::class, 'store'])->name('assignment.store')->middleware('permission:student_assignments,edit');

    Route::get('/payment', [StudentPaymentController::class, 'index'])->name('payment.index')->middleware('permission:student_payments,view');

    Route::get('/course', [StudentCourseController::class, 'index'])->name('course')->middleware('permission:student_courses,view');

    /* Email Log Routes */
    Route::group(['middleware' => ['permission:student_emails,view']], function(){
        Route::get('email-log', [StudentEmailLogController::class, 'index'])->name('email-log.index');
        Route::get('email-log/{id}', [StudentEmailLogController::class, 'show'])->name('email-log.show');
        Route::get('email-log/{emailLog}/inline', [StudentEmailLogController::class, 'inline'])->name('email-log.inline');
    });

});



/* Instructor Routes */

Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'instructor', 'as' => 'instructor.'], function(){
    
    Route::middleware(['auth', 'role:instructor'])->group(function () {
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    });

    /* Profile Routes */
    Route::group(['middleware' => ['permission:instructor_profile,view']], function(){
        Route::get('/profile', [InstructorProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [InstructorProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [InstructorProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/update-social', [InstructorProfileController::class, 'updateSocial'])->name('profile.update-social');
        Route::post('/profile/update-avatar', [InstructorProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    });

    /* Group and Group Share Routes */
    Route::resource('groups', InstructorGroupController::class)->middleware('permission:instructor_groups,view');
    // Nested resource: groups.group-shares, with shallow routing
    Route::resource('groups.group-shares', InstructorGroupShareController::class)->middleware('permission:instructor_group_shares,view');

    Route::get('/students', [InstructorStudentController::class, 'index'])->name('students.index')->middleware('permission:instructor_students,view');

    /* Assignment Routes */
    Route::get('assignment', [InstructorAssignmentController::class, 'index'])->name('assignment.index')->middleware('permission:instructor_assignments,view');
    Route::get('assignment/{submission}/edit', [InstructorAssignmentController::class, 'edit'])->name('assignment.edit')->middleware('permission:instructor_assignments,edit');
    Route::put('assignment/{module}/update', [InstructorAssignmentController::class, 'update'])->name('assignment.update')->middleware('permission:instructor_assignments,edit');
    Route::delete('assignment/evaluation/{submission}', [InstructorAssignmentController::class, 'destroyEvaluation'])->name('assignment.evaluation.destroy')->middleware('permission:instructor_assignments,delete');



});

/* Agent Routes */

Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'agent', 'as' => 'agent.'], function(){
    
    Route::middleware(['auth', 'role:agent'])->group(function () {
        Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    });

    /* Profile Routes */
    Route::group(['middleware' => ['permission:agent_profile,view']], function(){
        Route::get('/profile', [AgentProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [AgentProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [AgentProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/update-social', [AgentProfileController::class, 'updateSocial'])->name('profile.update-social');
        Route::post('/profile/update-avatar', [AgentProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    });

    Route::get('/registration', [AgentRegistrationController::class, 'index'])->name('registration.index')->middleware('permission:agent_registrations,view');

});



/* Admin Routes */
Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'admin', 'as' => 'admin.'], function () {

    // Dashboard (role-gated, as before)
    Route::middleware(['role:admin,manager,sales'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    // Profile
    Route::get('/profile', [AdminProfileController::class, 'edit'])
        ->name('profile.edit')->middleware('permission:admin_profile,view');
    Route::put('/profile', [AdminProfileController::class, 'update'])
        ->name('profile.update')->middleware('permission:admin_profile,edit');

    /* User Routes */
    Route::resource('user', AdminUserController::class)
        ->middleware('permission:admin_users,view');
    // user permission editor
    Route::get('user/{user}/permission', [AdminUserController::class, 'editPermissions'])
        ->name('user.permission.edit')->middleware('permission:admin_users,edit');
    Route::put('user/{user}/permission', [AdminUserController::class, 'updatePermissions'])
        ->name('user.permission.update')->middleware('permission:admin_users,edit');

    /* Student Routes */
    Route::resource('student', AdminStudentController::class)->middleware('permission:admin_students,view');

    /* Instructor Routes */
    Route::resource('instructor', AdminInstructorController::class)->middleware('permission:admin_instructors,view');

    /* Agent Routes */
    Route::resource('agent', AdminAgentController::class)->middleware('permission:admin_agents,view');

    /* Manager Routes */
    Route::resource('manager', AdminManagerController::class)->middleware('permission:admin_managers,view');

    /* Course Routes */
    Route::resource('course', AdminCourseController::class)->middleware('permission:admin_courses,view');

    /* Module Routes */
    Route::resource('module', AdminModuleController::class)->middleware('permission:admin_modules,view');

    /* Lesson Routes */
    Route::resource('lesson', AdminLessonController::class)->middleware('permission:admin_lessons,view');

    /* Payment Routes */
    Route::resource('payment', AdminPaymentController::class)->middleware('permission:admin_payments,view');

    /* Commission Routes */
    Route::resource('commission', AdminCommissionController::class)->middleware('permission:admin_commissions,view');

    /* Expense Routes */
    Route::resource('expense', AdminExpenseController::class)->middleware('permission:admin_expenses,view');

    /* Income Routes */
    Route::resource('income', AdminIncomeController::class)->middleware('permission:admin_incomes,view');

    /* Student Recruitment Routes */
    Route::resource('recruitment', AdminRecruitmentController::class)->middleware('permission:admin_recruitments,view');

    /* Graduate Routes */
    Route::resource('graduate', AdminGraduateController::class)->middleware('permission:admin_graduates,view');

    /* Email Logs */
    Route::group(['middleware' => ['permission:admin_email_logs,view']], function () {
        Route::get('email-log', [AdminEmailLogController::class, 'index'])->name('email-log.index');
        Route::get('email-log/{id}', [AdminEmailLogController::class, 'show'])->name('email-log.show');
        Route::get('email-log/{emailLog}/inline', [AdminEmailLogController::class, 'inline'])->name('email-log.inline');
    });

    /* Settings (gate whole section) */
    Route::group(['middleware' => ['permission:admin_settings,view']], function () {
        Route::get('setting', [AdminSettingController::class, 'index'])->name('setting.index');

        Route::resource('setting-college', AdminCollegeController::class);
        Route::resource('setting-awarding-body', AdminAwardingBodyController::class);
        Route::resource('setting-course-category', AdminCourseCategoryController::class);
        Route::resource('setting-course-level', AdminCourseLevelController::class);
        Route::resource('setting-document-category', AdminDocumentCategoryController::class);
        Route::resource('setting-social-platform', AdminSocialPlatformController::class);
        Route::resource('setting-expense-category', AdminExpenseCategoryController::class);
        Route::resource('setting-income-category', AdminIncomeCategoryController::class);
        Route::resource('setting-user-role', AdminUserRoleController::class);
        Route::resource('setting-user-permission', AdminUserPermissionController::class);
        Route::resource('setting-user-status', AdminUserStatusController::class);
        Route::resource('setting-payment-status', AdminPaymentStatusController::class);
        Route::resource('setting-student-batch', AdminStudentBatchController::class);
        Route::resource('setting-student-group', AdminStudentGroupController::class);
        Route::resource('setting-country', AdminCountryController::class);
        Route::resource('setting-recruitment-source', AdminRecruitmentSourceController::class);
        Route::resource('setting-recruitment-status', AdminRecruitmentStatusController::class);
    });

    /* Bulk Email */
    Route::prefix('bulk-email')->group(function () {
        Route::get('/',  [AdminBulkEmailController::class, 'create'])
            ->name('bulk-email.create')->middleware('permission:admin_send_bulk_emails,view');
        Route::post('/', [AdminBulkEmailController::class, 'store'])
            ->name('bulk-email.store')->middleware('permission:admin_send_bulk_emails,create');
    });

    // Email attachments (keep with email-log permissions)
    Route::get('email-log/{emailLog}/attachments/{index}',
        [AdminEmailLogController::class, 'downloadAttachment'])
        ->whereNumber('emailLog')->whereNumber('index')
        ->name('email-log.attachment.download')
        ->middleware('permission:admin_email_logs,view');

    // Impersonation (admin only)
    Route::middleware(['permission:admin_impersonate_users,view'])->group(function () {
        Route::get('users/{user}/impersonate', [AdminImpersonationController::class, 'quickStart'])
            ->whereNumber('user')->name('impersonate.quick');

        Route::post('users/{user}/impersonate-token', [AdminImpersonationController::class, 'createToken'])
            ->whereNumber('user')->name('impersonate.token');

        Route::get('impersonate/start/{token}', [AdminImpersonationController::class, 'start'])
            ->where('token', '[A-Za-z0-9]{32,128}')
            ->name('impersonate.start');
    });

    // Stop impersonating
    Route::post('impersonate/stop', [AdminImpersonationController::class, 'stop'])
        ->name('impersonate.stop');
});



require __DIR__.'/auth.php';
