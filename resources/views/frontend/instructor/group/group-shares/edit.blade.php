@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        DASHBOARD OVERVIEW START
    ============================-->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">

                @include('frontend.instructor.sidebar')

                <div class="col-xl-10 col-md-8">

                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top">
                            <div class="wsus__dashboard_heading relative">
                                <h5>Edit Share</h5>
                                <p>{{ str_replace('_', ' ', $group->name) }}</p>
                            </div>
                        </div>

                        <div class="dashboard_add_courses">

                            <div class="add_course_basic_info">

                                <form action="{{ route('instructor.groups.group-shares.update',['group' => $group->id, 'group_share' => $share->id]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')                               

                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="wsus__login_form_input">
                                                <label class="label-required">Title</label>
                                                <input type="text" name="title" value="{{ $share->title }}">
                                                <x-input-error :messages="$errors->get('title')" class="mt-2 text-danger small" />
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="wsus__login_form_input">
                                                <label>Content</label>
                                                <input type="text" name="content" value="{{ $share->content }}">
                                                <x-input-error :messages="$errors->get('content')" class="mt-2 text-danger small" />
                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="wsus__login_form_input">
                                                <label>Link</label>
                                                <input type="text" name="link" value="{{ $share->link }}">
                                                <x-input-error :messages="$errors->get('link')" class="mt-2 text-danger small" />
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-12">
                                            <div class="wsus__login_form_input">
                                                <label for="#">File 
                                                    @if($share->file)
                                                        : <a href="{{ asset($share->file) }}" target="_blank">{{ basename($share->file) }}</a>
                                                    @endif
                                                </label>
                                                <input type="file" name="file" class="form-control">
                                                <x-input-error :messages="$errors->get('file')" class="mt-2 text-danger small" />
                                            </div>
                                        </div>
                                        
                                       
                                    </div>

                                    <div class="row mt-3">
                                        
                                        <div class="col-xl-12">
                                            <div class="add_course_basic_info_input">
                                                <button type="submit" class="common_btn mt_20">Save</button>
                                            </div>
                                        </div>
                                        
                                    </div>

                                </form>

                            </div>

                        </div>
                    </div>

                </div>

                

            </div>
        </div>
    </section>
    <!--===========================
        DASHBOARD OVERVIEW END
    ============================-->


    


@endsection
