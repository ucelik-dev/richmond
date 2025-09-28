@extends('admin.layouts.master')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">SEND BULK EMAIL</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.expense.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="add_course_basic_info">
                    <form method="post" action="{{ route('admin.bulk-email.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label class="form-label label-required">From name</label>
                                    <input name="from_name" class="form-control" value="{{ old('from_name', config('mail.from.name')) }}">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label class="form-label label-required">From email</label>
                                    <input name="from_email" type="email" class="form-control" value="{{ old('from_email', config('mail.from.address')) }}">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="general_form_input">
                                    <label class="form-label label-required">Subject</label>
                                    <input name="subject" class="form-control" required value="{{ old('subject') }}">
                                    @error('subject') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="general_form_input">
                                    <label class="label-required">Message</label>
                                    <textarea rows="4" name="content" class="form-control summernote">{{ old('content') }}</textarea>
                                    <x-input-error :messages="$errors->get('content')" class="mt-2 text-danger small" />
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="general_form_input">
                                    <label class="form-label">Attachments (optional)</label>
                                    <input type="file" name="attachments[]" class="form-control" multiple>
                                    <div class="form-text">You can select multiple files. Total size is typically limited to ~25 MB.</div>
                                    <x-input-error :messages="$errors->get('attachments')" class="mt-2 text-danger small" />
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="general_form_input">
                                    <label class="form-label label-required">Recipients (paste, any of: newline/comma/semicolon)</label>
                                    <textarea name="emails" class="form-control" rows="8" placeholder="alice@example.com&#10;bob@example.com">{{ old('emails') }}</textarea>
                                    @error('emails') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Queue emails</button>
                        
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