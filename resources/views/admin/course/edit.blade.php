@extends('admin.layouts.master')



@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">COURSE UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.course.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.course.update',$course->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Modules *</label>
                                        <select class="form-control select2-multiple" name="modules[]" multiple>
                                            @foreach ($modules as $module)
                                                <option value="{{ $module->id }}" @selected(in_array($module->id, $selectedModules))>
                                                    {{ $module->title }} @if($module->awardingBody) ({{ $module->awardingBody?->name }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('modules')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Title</label>
                                        <input type="text" name="title" value="{{ $course->title }}" class="form-control">
                                        <x-input-error :messages="$errors->get('title')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Extended Title</label>
                                        <input type="text" name="extended_title" value="{{ $course->extended_title }}" class="form-control">
                                        <x-input-error :messages="$errors->get('extended_title')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Code</label>
                                        <input type="text" name="code" value="{{ $course->code }}" class="form-control">
                                        <x-input-error :messages="$errors->get('code')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Credits</label>
                                        <input type="text" name="credits" value="{{ $course->credits }}" class="form-control">
                                        <x-input-error :messages="$errors->get('credits')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Level</label>
                                        <select class="form-control form-select" name="level_id">
                                            @foreach ($levels as $level)
                                                    <option @selected($course->level_id == $level->id) value="{{ $level->id }}">{{ $level->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('level_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Category</label>
                                        <select class="form-control form-select" name="category_id">
                                            @foreach ($categories as $category)
                                                    <option @selected($course->category_id == $category->id) value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('category_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Awarding Body</label>
                                        <select class="form-control form-select" name="awarding_body_id">
                                            @foreach ($awardingBodies as $awardingBody)
                                                    <option @selected($course->awarding_body_id == $awardingBody->id) value="{{ $awardingBody->id }}">{{ $awardingBody->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('awarding_body_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Price</label>
                                        <input type="text" name="price" value="{{ $course->price }}" class="form-control">
                                        <x-input-error :messages="$errors->get('price')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Discount</label>
                                        <input type="text" name="discount" value="{{ $course->discount }}" class="form-control">
                                        <x-input-error :messages="$errors->get('discount')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Description</label>
                                        <textarea rows="4" name="description" class="form-control summernote">{{ $course->description }}</textarea>
                                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Overview</label>
                                        <textarea rows="8" name="overview" class="form-control summernote">{{ $course->overview }}</textarea>
                                        <x-input-error :messages="$errors->get('overview')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Overview Details</label>
                                        <textarea rows="8" name="overview_details" class="form-control summernote">{{ $course->overview_details }}</textarea>
                                        <x-input-error :messages="$errors->get('overview_details')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Learning Outcomes</label>
                                        <textarea rows="8" name="learning_outcomes" class="form-control summernote">{{ $course->learning_outcomes }}</textarea>
                                        <x-input-error :messages="$errors->get('learning_outcomes')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Thumbnail</label><br>
                                        <img src="{{ asset($course->thumbnail) }}"
                                            style="width: 150px !important; height: 150px !important; object-fit: contain !important; display: inline-block !important;">
                                        <input type="file" name="thumbnail" class="form-control mt-2">
                                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Logo</label><br>
                                        <img src="{{ asset($course->logo) }}"
                                            style="width: 150px !important; height: 150px !important; object-fit:contain !important; display: inline-block !important;">
                                        <input type="file" name="logo" class="form-control mt-2">
                                        <x-input-error :messages="$errors->get('logo')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Handbook</label>
                                        <input type="file" name="handbook_file" class="form-control">
                                        <p class="mt-2"><a href="{{ asset($course->handbook_file) }}" target="_blank">{{ basename($course->handbook_file) }}</a></p>
                                        <x-input-error :messages="$errors->get('handbook_file')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Mapping Document</label>
                                        <input type="file" name="mapping_document" class="form-control">
                                        <p class="mt-2"><a href="{{ asset($course->mapping_document) }}" target="_blank">{{ basename($course->mapping_document) }}</a></p>
                                        <x-input-error :messages="$errors->get('mapping_document')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Assignment Specification</label>
                                        <input type="file" name="assignment_specification" class="form-control">
                                        <p class="mt-2"><a href="{{ asset($course->assignment_specification) }}" target="_blank">{{ basename($course->assignment_specification) }}</a></p>
                                        <x-input-error :messages="$errors->get('assignment_specification')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Curriculum</label>
                                        <input type="file" name="curriculum" class="form-control">
                                        <p class="mt-2"><a href="{{ asset($course->curriculum) }}" target="_blank">{{ basename($course->curriculum) }}</a></p>
                                        <x-input-error :messages="$errors->get('curriculum')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Demo Video Storage</label>
                                        <select class="form-control form-select" name="demo_video_storage">
                                            <option value=""> Please Select </option>
                                            <option @selected($course->demo_video_storage == 'youtube') value="youtube"> Youtube </option>
                                            <option @selected($course->demo_video_storage == 'vimeo') value="vimeo"> Vimeo </option>
                                            <option @selected($course->demo_video_storage == 'external_link') value="external_link"> External Link </option>
                                        </select>
                                        <x-input-error :messages="$errors->get('demo_video_storage')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Demo Video Path</label>
                                        <input type="text" name="demo_video_source" class="form-control" value="{{ $course->demo_video_source }}">
                                        <x-input-error :messages="$errors->get('demo_video_source')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Status</label>
                                        <select class="form-control form-select" name="status">
                                            <option value=""> Please Select </option>
                                            @foreach ($statuses as $status)
                                                <option @selected($course->status == $status) value="{{ $status }}"> {{ ucwords($status) }} </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Show in select</label>
                                        <select class="form-control form-select" name="show_in_select">
                                            <option @selected($course->show_in_select == true) value="1"> Yes </option>
                                            <option @selected($course->show_in_select == false) value="0"> No </option>
                                        </select>
                                        <x-input-error :messages="$errors->get('show_in_select')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6 mt-2">
                                    <div class="add_course_more_info_checkbox">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="completion_test" value="1" id="completion_test" @checked($course->completion_test)>
                                            <label for="completion_test">Completion Test</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="completion_certificate" value="1" id="completion_certificate" @checked($course->completion_certificate)>
                                            <label for="completion_certificate">Completion Certificate</label>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-xl-12">
                                    <div class="add_course_basic_info_input">
                                        <button type="submit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Update</button>
                                    </div>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script>
    $(function () {
    $('.summernote').summernote({
        height: 250,
        placeholder: 'Type here…'
    });
    });
</script>

@endpush