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
                                <h5>Group Shares</h5>
                                <p>{{ str_replace('_', ' ', $group->name) }}</p>
                                @can('create_instructor_group_shares')
                                    <a class="common_btn" href="{{ route('instructor.groups.group-shares.create', $group->id) }}">+ add new</a>
                                @endcan
                            </div>
                        </div>

                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive p-4">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width:10px">#</th>
                                                    <th>TITLE</th>
                                                    <th>CONTENT</th>
                                                    <th>LINK</th>
                                                    <th>FILE</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               
                                                @foreach($shares as $share)
                                                    <tr>
                                                        <td><p>{{ $loop->iteration }}</p></td>
                                                        
                                                        <td><p>{{ $share->title }}</p></td>
                                                       
                                                        <td><p>{{ $share->content }}</p></td>

                                                        <td><p><a href="{{ $share->link }}" target="_blank">{{ $share->link }}</p></td>

                                                        <td><p><a href="{{ asset($share->file) }}" target="_blank">{{ basename($share->file) }}</p></td>
                                                        
                                                            <td class="text-nowrap">
                                                                @can('edit_instructor_group_shares')
                                                                    <a href="{{ route('instructor.groups.group-shares.edit', ['group' => $share->group_id, 'group_share' => $share->id]) }}" class="btn-sm btn-primary me-2 text-decoration-none">
                                                                        <i class="fa-solid fa-pen-to-square fa-md"></i>
                                                                    </a>
                                                                @endcan

                                                                @can('delete_instructor_group_shares')
                                                                    <a href="{{ route('instructor.groups.group-shares.destroy', ['group' => $share->group_id, 'group_share' => $share->id]) }}" class="text-red delete-item text-decoration-none">
                                                                        <i class="fa-solid fa-trash-can fa-md text-danger"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>

                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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