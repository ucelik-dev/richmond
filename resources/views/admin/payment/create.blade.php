@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PAYMENT CREATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.payment.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="add_course_basic_info">
                        <form action="{{ route('admin.payment.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
           
                            <div class="row">
                                <div class="col-xl-8">
                                    <div class="general_form_input">
                                        <label class="label-required">Name</label>
                                        <select name="student_course_id" class="form-control form-select" id="studentCourseSelect">
                                            <option value="">Select student & course</option>
                                            @foreach ($students as $student)
                                                @foreach ($student->enrollments as $enrollment)
                                                    <option 
                                                        value="{{ $student->id }}|{{ $enrollment->course_id }}"
                                                        data-course-price="{{ $enrollment->course->price }}"
                                                    >
                                                        {{ $student->name }} - {{ $enrollment->course->title }} ({{ $enrollment->course->level->name }})
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                        
                                        <input type="hidden" name="user_id" id="userIdInput">
                                        <input type="hidden" name="course_id" id="courseIdInput">
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Status</label>
                                        <select class="form-control form-select" name="status_id">
                                            <option value=""> Please Select </option>
                                            @foreach ($paymentStatuses as $paymentStatus)
                                                <option value="{{ $paymentStatus->id }}"> {{ ucwords($paymentStatus->name) }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                               
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Amount</label>
                                        <input type="number" id="amount" name="amount" class="form-control">
                                        <x-input-error :messages="$errors->get('amount')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Discount</label>
                                        <input type="number" id="discount" name="discount" class="form-control" >
                                        <x-input-error :messages="$errors->get('discount')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Total</label>
                                        <input type="number" id="total" name="total" class="form-control" readonly>
                                        <x-input-error :messages="$errors->get('total')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label>Notes</label>
                                        <textarea rows="4" name="notes" class="form-control">{{ old('notes') }}</textarea>
                                        <x-input-error :messages="$errors->get('notes')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Create</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('studentCourseSelect');
        const amountInput = document.getElementById('amount');
        const discountInput = document.getElementById('discount');
        const totalInput = document.getElementById('total');
        const userIdInput = document.getElementById('userIdInput');
        const courseIdInput = document.getElementById('courseIdInput');

        function updateTotal() {
            const amount = parseFloat(amountInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            totalInput.value = (amount - discount).toFixed(2);
        }

        select.addEventListener('change', function () {
            const selectedOption = select.options[select.selectedIndex];
            const [userId, courseId] = selectedOption.value.split('|');

            userIdInput.value = userId;
            courseIdInput.value = courseId;

            const price = selectedOption.getAttribute('data-course-price') || '';
            amountInput.value = price;
            updateTotal();
        });

        amountInput.addEventListener('input', updateTotal);
        discountInput.addEventListener('input', updateTotal);
    });
</script>
@endpush


