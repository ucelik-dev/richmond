@extends('admin.layouts.master')



@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">LESSON UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.lesson.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.lesson.update', $lesson->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Modules</label>
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
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Title</label>
                                        <input type="text" name="title" class="form-control" value="{{ $lesson->title }}">
                                        <x-input-error :messages="$errors->get('title')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input mb-0">
                                        <label for="#" class="label-required">Content</label>
                                        <textarea rows="4" name="content" class="form-control summernote">{!! $lesson->content !!}</textarea>
                                        <x-input-error :messages="$errors->get('content')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input mt-3">
                                        <label for="#">Video URL</label>
                                        <input type="text" name="video_url" class="form-control" value="{{ $lesson->video_url }}">
                                        <x-input-error :messages="$errors->get('video_url')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                            
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Status</label>
                                        <select class="form-control form-select" name="status">
                                            <option @selected($lesson->status === 1) value="1">Active</option>
                                            <option @selected($lesson->status === 0) value="0">Inactive</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Order</label>
                                        <input type="number" name="order" class="form-control" value="{{ $lesson->order }}">
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