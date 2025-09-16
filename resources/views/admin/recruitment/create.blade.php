@extends('admin.layouts.master')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">RECRUITMENT CREATE</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.recruitment.index') }}" class="btn btn-default">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="add_course_basic_info">
                    <form action="{{ route('admin.recruitment.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-4">
                                <div class="general_form_input">
                                    <label class="label-required">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="general_form_input">
                                    <label class="label-required">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="general_form_input">
                                    <label>Email</label>
                                    <input type="text" name="email" value="{{ old('email') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="general_form_input">
                                    <label>Country</label>
                                    <select class="form-control form-select" name="country_id">
                                        <option value=""> Please Select </option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-xl-4">
                                <div class="general_form_input">
                                    <label class="label-required">Source</label>
                                    <select class="form-control form-select" name="source_id">
                                        <option value=""> Please Select </option>
                                        @foreach ($recruitmentSources as $recruitmentSource)
                                            <option value="{{ $recruitmentSource->id }}">
                                                {{ $recruitmentSource->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="general_form_input">
                                    <label class="label-required">Status</label>
                                    <select class="form-control form-select" name="status_id">
                                        <option value=""> Please Select </option>
                                        @foreach ($recruitmentStatuses as $recruitmentStatus)
                                            <option value="{{ $recruitmentStatus->id }}">
                                                {{ $recruitmentStatus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>

                        <button type="submit" class="btn btn-default mt-2">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const recurringOptions = document.getElementById('recurringOptions');
    const salarySelect = document.getElementById('salaryUserSelect');

    document.getElementById('categorySelect').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const isRecurring = selected.dataset.recurring === "1";

        if (isRecurring) {
            recurringOptions.style.display = 'flex';

            // Show salary select only if category name includes "salary"
            const text = selected.text.toLowerCase();
            if (text.includes('salary')) {
                salarySelect.style.display = 'block';
            } else {
                salarySelect.style.display = 'none';
            }
        } else {
            recurringOptions.style.display = 'none';
            salarySelect.style.display = 'none';
        }
    });
</script>
@endpush
