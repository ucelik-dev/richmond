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
                                <h5>Finance</h5>
                            </div>
                        </div>

                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12 col-xl-6 p-4">
                                    <div class="table-responsive p-1 border rounded">
                                        <table class="table table-bordered" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">College Bank Account</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <tr>
                                                    <td class="align-middle">
                                                        {!! $college->bank_account !!}
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                                <div class="col-12 col-xl-6 p-4">
                                    <div class="table-responsive p-1 border rounded">
                                        <table class="table table-bordered" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">College Invoice Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <tr>
                                                    <td>
                                                        {!! $college->invoice_data !!}
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="wsus__dash_course_table">

                            <form
                                action="{{ route('agent.finance.update', $college->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                    <div class="row">

                                        <div class="col-12 col-xl-6 p-4">
                                            <div class="table-responsive p-1 border rounded">
                                                <table class="table table-bordered p-0" id="documentsTable">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-nowrap">Your Bank Account</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="align-middle p-1">
                                                                <textarea rows="8" name="bank_account" class="form-control summernote">{{ $agent->bank_account }}</textarea>
                                                                <x-input-error :messages="$errors->get('bank_account')" class="mt-2 text-danger small" />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                        <div class="col-12 col-xl-6 p-4">
                                            <div class="table-responsive p-1 border rounded">
                                                <table class="table table-bordered" id="documentsTable">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-nowrap">Your Invoice Details</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="align-middle p-1">
                                                                <textarea rows="8" name="invoice_data" class="form-control summernote">{{ $agent->invoice_data }}</textarea>
                                                                <x-input-error :messages="$errors->get('invoice_data')" class="mt-2 text-danger small" />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                        @if(auth()->user()?->canResource('agent_finance','edit'))
                                            <div class="col-12 px-4 pb-4 pt-0">
                                                <button type="submit" class="common_btn mt-0">Update</button>
                                            </div>
                                        @endif

                                    </div>

                            </form>

                        </div>

                    </div>


                </div>




            </div>

        </div>
        </div>
    </section>
    <!-- DASHBOARD OVERVIEW END -->
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.summernote').summernote({
                height: 250,
                placeholder: 'Type here…'
            });
        });
    </script>
@endpush
