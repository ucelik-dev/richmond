@extends('frontend.layouts.master')

@section('content')

    <!-- DASHBOARD OVERVIEW START -->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">

                @include('frontend.agent.sidebar')

                <div class="col-xl-10 col-md-8 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top d-flex flex-wrap justify-content-between">
                            <div class="wsus__dashboard_heading">
                                <h5>Documents</h5>
                            </div>
                        </div>


                            <div class="wsus__dash_course_table">
                                <div class="row">
                                    <div class="col-12 p-4">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="documentsTable">
                                                <thead>
                                                    <tr>
                                                        <th class="text-nowrap">Category</th>
                                                        <th class="text-nowrap">File</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @forelse ($agent->documents as $doc)
                                                        <tr>
                                                            <td class="align-middle">
                                                                {{ $doc->category?->name ?? '—' }}
                                                            </td>
                                                            <td>
                                                                @if(!empty($doc->path))
                                                                    <a href="{{ asset($doc->path) }}" target="_blank">
                                                                        View File
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">No file uploaded</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="2" class="text-muted">No documents found.</td>
                                                        </tr>
                                                    @endforelse

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
    <!-- DASHBOARD OVERVIEW END -->
@endsection
