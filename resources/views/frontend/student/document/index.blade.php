@extends('frontend.layouts.master')

@section('content')

    <!-- DASHBOARD OVERVIEW START -->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">

                @include('frontend.student.sidebar')

                <div class="col-xl-10 col-md-8 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top d-flex flex-wrap justify-content-between">
                            <div class="wsus__dashboard_heading">
                                <h5>Update Your Documents</h5>
                                <p>Add or update your official documents.</p>
                            </div>
                        </div>


                        <form action="{{ route('student.document.update') }}" method="POST" enctype="multipart/form-data" >
                        @csrf

                            <div class="wsus__dash_course_table">
                                <div class="row">
                                    <div class="col-12 p-4">
                                        <div class="table-responsive p-1 border rounded">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-nowrap">Document Category</th>
                                                        <th class="text-nowrap">File</th>
                                                        <th class="text-nowrap">Upload</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach ($documentCategories as $index => $documentCategory)
                                                        @php
                                                            $document = $student->documents->firstWhere('category_id', $documentCategory->id);
                                                        @endphp
                                                        <tr>
                                                            <td class="text-nowrap align-middle p-2">
                                                                <input type="hidden" name="documents[{{ $index }}][category_id]" value="{{ $documentCategory->id }}">
                                                                <input type="text" class="form-control" value="{{ $documentCategory->name }}" disabled>
                                                            </td>
                                                            <td class="text-nowrap p-2">
                                                                @if($document)
                                                                    <a href="{{ asset($document->path) }}" target="_blank">{{ basename($document->path) }}</a>
                                                                @else
                                                                    <span class="text-muted">No file uploaded</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-nowrap p-2">
                                                                <input type="file" name="documents[{{ $index }}][file]" class="form-control" />
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                        @can('edit_student_documents')
                                            <div class="wsus__dashboard_profile_update_btn">
                                                <button type="submit" class="common_btn">Update Documents</button>
                                            </div>
                                        @endcan
                                    </div>
                                    
                                </div>
                            </div>

                        </form>
                            
                    
                    </div>

                    

                 
                </div>

            </div>
        </div>
    </section>
    <!-- DASHBOARD OVERVIEW END -->
@endsection
