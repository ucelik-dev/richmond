@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">COMMISSION UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.commission.index') }}" class="btn btn-default">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="add_course_basic_info">
                        <form action="{{ route('admin.commission.update', $commission->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                
                                <div class="col-xl-12">

                                    <div class="table-responsive">
                                        <table class="table table-vcenter table-bordered mb-5">
                                            <thead>
                                                <tr>
                                                    <th>STUDENT NAME</th>
                                                    <th>STUDENT COURSE</th>
                                                    <th>STUDENT COURSE PRICE<small style="text-transform: capitalize; font-weight:normal"> (Price - Discount = Total)</small></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap">{{ $commission->payment->user->name }}</td>
                                                    <td class="text-nowrap">{{ $commission->payment->course->title }} ({{ $commission->payment->course->level->name }})</td>
                                                    <td class="text-nowrap">{{ currency_format($commission->payment->amount) }} - {{ currency_format($commission->payment->discount) }} = {{ currency_format($commission->payment->total) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                
                                {{-- Commission To (User OR External Name) --}}
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label>Commission To <small>(User)</small></label>
                                        <select name="user_id" id="userSelect" class="form-control form-select">
                                            <option value="">Select a user (optional)</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}" @selected(optional($commission->user)->id === $u->id)>
                                                {{ $u->name }} ({{ ucwords($u->mainRole->name) ?? '—' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label>Commission To <small>(External Person)</small></label>
                                        <input type="text" name="payee_name" id="externalName" 
                                            value="{{ $commission->payee_name }}"
                                            class="form-control" 
                                            {{ $commission->user_id ? 'disabled' : '' }}>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Commission Amount</label>
                                        <input type="number" id="amount" name="amount" class="form-control" value="{{ $commission->amount }}">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Status</label>
                                        <select name="status" class="form-control form-select">
                                            <option value="paid" {{ $commission->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="unpaid" {{ $commission->status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Paid at</label>
                                        <input type="date" name="paid_at" class="form-control" value="{{ $commission->paid_at ? \Carbon\Carbon::parse($commission->paid_at)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input d-flex align-items-end"> {{-- Added d-flex and align-items-end --}}
                                        <div class="flex-grow-1"> {{-- Added flex-grow-1 and me-2 --}}
                                            <label>Note</label>
                                            <textarea name="note" class="form-control">{{ $commission->note }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-default mt-2">Update</button>
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
        const userSelect = document.getElementById('userSelect');
        const externalInput = document.getElementById('externalName');

        function toggleExternal() {
            if (userSelect.value) {
                externalInput.value = ''; // clear value
                externalInput.setAttribute('disabled', 'disabled');
            } else {
                externalInput.removeAttribute('disabled');
            }
        }

        userSelect.addEventListener('change', toggleExternal);

        // run once on page load (in case editing existing record)
        toggleExternal();
    });
</script>


@endpush