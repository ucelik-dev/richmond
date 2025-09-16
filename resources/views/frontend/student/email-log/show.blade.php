@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        DASHBOARD OVERVIEW START
    ============================-->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">
                
                @include('frontend.student.sidebar')

                <div class="col-xl-10 col-md-8">
                    
                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top">
                            <div class="wsus__dashboard_heading d-flex justify-content-between align-items-center" style="height: 30px">
                                <h5 class="m-0 flex-grow-1">Email Preview</h5>
                                <a class="common_btn ms-auto px-3 py-2" href="{{ route('student.email-log.index') }}">
                                    <i class="fa-solid fa-arrow-left me-2"></i>Back
                                </a>
                            </div>
                        </div>

                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12 p-4">
                
                                    <div class="table-responsive p-1 mb-5 border rounded">

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>SUBJECT</th>
                                                    <th>EMAIL</th>
                                                    <th>SENT AT</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap p-2"><p>{{ $emailLog->subject ?? '-' }}</p></td>
                                                    <td class="text-nowrap p-2"><p>{{ $emailLog->to ?? '-' }}</p></td>
                                                    <td class="text-nowrap p-2"><p>{{ \Carbon\Carbon::parse($emailLog->sent_at)->format('d-m-Y H:m:i') }}</p></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="p-2">
                                                        <iframe
                                                            id="emailPreview"
                                                            class="w-100 border-0"
                                                            style="min-height: 900px;"
                                                            sandbox="allow-same-origin allow-popups"
                                                            src="{{ route('student.email-log.inline', $emailLog) }}">
                                                        </iframe>
                                                    </td>
                                                </tr>
                                            
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
