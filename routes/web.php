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
    Route::group(['middleware' => ['can:view_student_profile']], function(){
        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [StudentProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [StudentProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/update-social', [StudentProfileController::class, 'updateSocial'])->name('profile.update-social');
        Route::post('/profile/update-avatar', [StudentProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    });


    Route::get('/document', [StudentDocumentController::class, 'index'])->name('document')->middleware('can:view_student_documents');
    Route::post('/document/update', [StudentDocumentController::class, 'updateDocument'])->name('document.update')->middleware('can:edit_student_documents');

    Route::get('assignment', [StudentAssignmentController::class, 'index'])->name('assignment.index')->middleware('can:view_student_assignments');
    Route::get('assignment/{module}/create', [StudentAssignmentController::class, 'create'])->name('assignment.create')->middleware('can:edit_student_assignments');
    Route::post('assignment/{module}', [StudentAssignmentController::class, 'store'])->name('assignment.store')->middleware('can:edit_student_assignments');

    Route::get('/payment', [StudentPaymentController::class, 'index'])->name('payment.index')->middleware('can:view_student_payments');

    Route::get('/course', [StudentCourseController::class, 'index'])->name('course')->middleware('can:view_student_courses');

    /* Email Log Routes */
    Route::group(['middleware' => ['can:view_student_emails']], function(){
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
    Route::group(['middleware' => ['can:view_instructor_profile']], function(){
        Route::get('/profile', [InstructorProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [InstructorProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [InstructorProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/update-social', [InstructorProfileController::class, 'updateSocial'])->name('profile.update-social');
        Route::post('/profile/update-avatar', [InstructorProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    });

    /* Group and Group Share Routes */
    Route::resource('groups', InstructorGroupController::class)->middleware('can:view_instructor_groups');
    // Nested resource: groups.group-shares, with shallow routing
    Route::resource('groups.group-shares', InstructorGroupShareController::class)->middleware('can:view_instructor_group_shares');

    Route::get('/students', [InstructorStudentController::class, 'index'])->name('students.index')->middleware('can:view_instructor_students');

    /* Assignment Routes */
    Route::get('assignment', [InstructorAssignmentController::class, 'index'])->name('assignment.index')->middleware('can:view_instructor_assignments');
    Route::get('assignment/{submission}/edit', [InstructorAssignmentController::class, 'edit'])->name('assignment.edit')->middleware('can:edit_instructor_assignments');
    Route::put('assignment/{module}/update', [InstructorAssignmentController::class, 'update'])->name('assignment.update')->middleware('can:edit_instructor_assignments');
    Route::delete('assignment/evaluation/{submission}', [InstructorAssignmentController::class, 'destroyEvaluation'])->name('assignment.evaluation.destroy')->middleware('can:delete_instructor_assignments');



});

/* Agent Routes */

Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'agent', 'as' => 'agent.'], function(){
    
    Route::middleware(['auth', 'role:agent'])->group(function () {
        Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    });

    /* Profile Routes */
    Route::group(['middleware' => ['can:view_agent_profile']], function(){
        Route::get('/profile', [AgentProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [AgentProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [AgentProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/update-social', [AgentProfileController::class, 'updateSocial'])->name('profile.update-social');
        Route::post('/profile/update-avatar', [AgentProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    });

    Route::get('/registration', [AgentRegistrationController::class, 'index'])->name('registration.index')->middleware('can:view_agent_registrations');

});



/* Admin Routes */

Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'admin', 'as' => 'admin.'], function(){    
    
    Route::middleware(['role:admin,manager,sales'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit')->middleware('can:view_admin_profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update')->middleware('can:edit_admin_profile');

    /* User Routes */
    Route::resource('user', AdminUserController::class)->middleware('can:index_admin_payments');
    // New routes for managing user permissions
    Route::get('user/{user}/permission', [AdminUserController::class, 'editPermissions'])->name('user.permission.edit');
    Route::put('user/{user}/permission', [AdminUserController::class, 'updatePermissions'])->name('user.permission.update');


    
    /* Student Routes */
    Route::resource('student', AdminStudentController::class)->middleware('can:index_admin_students');

    /* Instructor Routes */
    Route::resource('instructor', AdminInstructorController::class)->middleware('can:index_admin_instructors');

    /* Agent Routes */
    Route::resource('agent', AdminAgentController::class)->middleware('can:index_admin_agents');

    /* Agent Routes */
    Route::resource('manager', AdminManagerController::class)->middleware('can:index_admin_managers');

    /* Course Routes */
    Route::resource('course', AdminCourseController::class)->middleware('can:index_admin_courses');

    /* Module Routes */
    Route::resource('module', AdminModuleController::class)->middleware('can:index_admin_modules');

    /* Lesson Routes */
    Route::resource('lesson', AdminLessonController::class)->middleware('can:index_admin_lessons');

    /* Payment Routes */
    Route::resource('payment', AdminPaymentController::class)->middleware('can:index_admin_payments');

    /* Commission Routes */
    Route::resource('commission', AdminCommissionController::class)->middleware('can:index_admin_commissions');

    /* Expense Routes */
    Route::resource('expense', AdminExpenseController::class)->middleware('can:index_admin_expenses');

    /* Income Routes */
    Route::resource('income', AdminIncomeController::class)->middleware('can:index_admin_incomes');

    /* Student Recruitment Routes */
    Route::resource('recruitment', AdminRecruitmentController::class)->middleware('can:index_admin_recruitments');

    /* Graduate Routes */
    Route::resource('graduate', AdminGraduateController::class)->middleware('can:index_admin_graduates');

    /* Email Log Routes */
    Route::group(['middleware' => ['can:index_admin_email_logs']], function(){
        Route::get('email-log', [AdminEmailLogController::class, 'index'])->name('email-log.index');
        Route::get('email-log/{id}', [AdminEmailLogController::class, 'show'])->name('email-log.show');
        Route::get('email-log/{emailLog}/inline', [AdminEmailLogController::class, 'inline'])->name('email-log.inline');
    });
    

    /* Setting Routes */
    Route::group(['middleware' => ['can:index_admin_settings']], function(){
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


     /* Bulk Email (create + send) */
     //Route::middleware(['can:send-bulk-email']) // adjust to your auth/permission
    Route::middleware(['can:send_bulk_emails'])
        ->prefix('bulk-email')
        ->group(function () {
            Route::get('/',  [AdminBulkEmailController::class, 'create'])->name('bulk-email.create');
            Route::post('/', [AdminBulkEmailController::class, 'store'])->name('bulk-email.store');
        });

    Route::get('email-log/{emailLog}/attachments/{index}', 
        [AdminEmailLogController::class, 'downloadAttachment'])
        ->whereNumber('emailLog')->whereNumber('index')
        ->name('email-log.attachment.download')
        ->middleware('can:index_admin_email_logs'); // adjust permission


    // Impersonation (admin only)
    Route::middleware(['can:impersonate_users'])->group(function () {
        // 1) One-click from tables: creates token then redirects to start
        Route::get('users/{user}/impersonate', [AdminImpersonationController::class, 'quickStart'])
            ->whereNumber('user')
            ->name('impersonate.quick');

        // 2) Manual: create token only (if you still need it anywhere)
        Route::post('users/{user}/impersonate-token', [AdminImpersonationController::class, 'createToken'])
            ->whereNumber('user')
            ->name('impersonate.token');

        // 3) Consume token & switch session
        Route::get('impersonate/start/{token}', [AdminImpersonationController::class, 'start'])
            ->where('token', '[A-Za-z0-9]{32,128}')
            ->name('impersonate.start');

        
    });

    // 4) Stop impersonating
    Route::post('impersonate/stop', [AdminImpersonationController::class, 'stop'])
        ->name('impersonate.stop');





});



require __DIR__.'/auth.php';
