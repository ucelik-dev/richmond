@extends('admin.layouts.master')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none mt-5">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                  
                    <h2 class="page-title">
                        Settings
                    </h2>
                    <hr class="mt-3 mb-1">
                </div>
                
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="col-12">
                    <div class="row row-cards">

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-college.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa-solid fa-school"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Colleges
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        
                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-awarding-body.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa-solid fa-building-columns"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Awarding Bodies
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-course-category.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-th-large"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Course Categories
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-course-level.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-signal"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Course Levels
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-document-category.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-folder-open"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Document Categories
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        
                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-social-platform.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-share-alt"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Social Platforms
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                      
                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-expense-category.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa-solid fa-money-bill-trend-up"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Expense Categories
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-income-category.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa-solid fa-money-bill-trend-up"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Income Categories
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        
                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-user-role.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fas fa-users"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            User Roles
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-user-permission.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fas fa-users"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            User Permissions
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-user-status.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            User Statuses
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-payment-status.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Payment Statuses
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-student-batch.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Student Batches
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-student-group.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-object-group"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Student Groups
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-country.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-globe"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Countries
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-recruitment-source.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-bullhorn"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Recruitment Sources
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-2">
                                <a href="{{  route('admin.setting-recruitment-status.index') }}" class="text-decoration-none">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span
                                                        class="btn-default avatar">
                                                        <i class="fa fa-tags"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-medium d-flex">
                                                        <div>
                                                            Recruitment Statuses
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        
                      
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
