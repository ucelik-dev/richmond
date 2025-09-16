@extends('frontend.layouts.master')

@section('content')

    <!-- DASHBOARD OVERVIEW START -->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">
                
                @include('frontend.student.sidebar')

                <div class="col-xl-10 col-md-8 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">
                    
                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top">
                            <div class="wsus__dashboard_heading relative">
                                <h5>Emails <small class="text-muted">({{ $emailLogs->count() }})</small></h5>
                            </div>
                        </div>


                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12 p-4">
                                    
                                    <div class="table-responsive p-1 mb-5 border rounded">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width:10px">#</th>
                                                    <th>SUBJECT</th>
                                                    <th>EMAIL</th>
                                                    <th>SENT AT</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($emailLogs as $emailLog)

                                                    <tr>
                                                        <td><p>{{ $loop->iteration }}</p></td>

                                                        <td class="text-nowrap p-2">
                                                            <p class="mb-1">{{ $emailLog->subject ?? '-' }}</p>
                                                        </td>

                                                        <td class="text-nowrap p-2">
                                                            <p class="mb-1">{{ $emailLog->to ?? '-' }}</p>
                                                        </td>

                                                        <td class="text-nowrap p-2">
                                                            <p class="mb-1">{{ \Carbon\Carbon::parse($emailLog->sent_at)->format('d-m-Y H:m:i') }}</p>
                                                        </td>

                                                        <td class="text-nowrap p-2">
                                                            <p class="mb-1">
                                                                <a href="{{ route('student.email-log.show', $emailLog->id) }}" class="btn-sm btn-primary me-2 text-decoration-none">
                                                                    <i class="fa-solid fa-eye me-2"></i></i>Preview
                                                                </a>
                                                            </p>
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
