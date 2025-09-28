@extends('admin.layouts.master')



@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">MODULE UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.module.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.module.update', $module->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Courses *</label>
                                        <select class="form-control select2-multiple" name="courses[]" multiple>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}" @selected(in_array($course->id, $selectedCourses))>
                                                    {{ $course->title }} ({{ $course->level->name }}) {{ $course->awardingBody?->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('courses')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Title *</label>
                                        <input type="text" name="title" class="form-control" value="{{ $module->title }}">
                                        <x-input-error :messages="$errors->get('title')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Level</label>
                                        <select class="form-control form-select" name="level_id">
                                            <option value="">Please select</option>
                                            @foreach($levels as $level)
                                                <option @selected($level->id === $module->level_id) value="{{ $level->id }}">{{ $level->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('level_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Awarding Body</label>
                                        <select class="form-control form-select" name="awarding_body_id">
                                            <option value="">Please select</option>
                                            @foreach($awardingBodies as $awardingBody)
                                                <option @selected($awardingBody->id === $module->awarding_body_id) value="{{ $awardingBody->id }}">{{ $awardingBody->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('awarding_body_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#">Description *</label>
                                        <textarea rows="4" name="description" class="form-control summernote">{!! $module->description !!}</textarea>
                                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#">Overview *</label>
                                        <textarea rows="8" name="overview" class="form-control summernote">{!! $module->overview !!}</textarea>
                                        <x-input-error :messages="$errors->get('overview')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#">Learning Outcomes *</label>
                                        <textarea rows="8" name="learning_outcomes" class="form-control summernote">{!! $module->learning_outcomes !!}</textarea>
                                        <x-input-error :messages="$errors->get('learning_outcomes')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Assignment File</label>
                                        <input type="file" name="assignment_file" class="form-control">
                                        <p class="mt-2"><a href="{{ asset($module->assignment_file) }}" target="_blank">{{ basename($module->assignment_file) }}</a></p>
                                        <x-input-error :messages="$errors->get('assignment_file')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Sample Assignment File</label>
                                        <input type="file" name="sample_assignment_file" class="form-control">
                                        <p class="mt-2"><a href="{{ asset($module->sample_assignment_file) }}" target="_blank">{{ basename($module->sample_assignment_file) }}</a></p>
                                        <x-input-error :messages="$errors->get('sample_assignment_file')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Video URL</label>
                                        <input type="text" name="video_url" class="form-control" value="{{ $module->video_url }}">
                                        <x-input-error :messages="$errors->get('video_url')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                            
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Status *</label>
                                        <select class="form-control form-select" name="status">
                                            <option @selected($module->status === 1) value="1">Active</option>
                                            <option @selected($module->status === 0) value="0">Inactive</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Order *</label>
                                        <input type="number" name="order" class="form-control" value="{{ $module->order }}">
                                        <x-input-error :messages="$errors->get('order')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <button type="submit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Update</button>
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