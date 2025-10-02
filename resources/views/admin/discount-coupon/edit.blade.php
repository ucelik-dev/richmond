@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">DISCOUNT COUPON UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.discount-coupon.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="add_course_basic_info">
                        <form action="{{ route('admin.discount-coupon.update', $discountCoupon->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Code</label>
                                        <input type="text" name="code" class="form-control" value="{{ old('code', $discountCoupon->code) }}" disabled>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Agent</label>
                                        <select name="agent_id" class="form-control form-select" required>
                                            <option value="">Select an agent</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}"
                                                    @selected((string)old('agent_id', $discountCoupon->agent_id) === (string)$agent->id)>
                                                    {{ $agent->name }} — {{ $agent->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Discount Type</label>
                                        @php $typeOld = old('discount_type', $discountCoupon->discount_type); @endphp
                                        <select name="discount_type" class="form-control form-select" required>
                                            <option value="percent" @selected($typeOld === 'percent')>Percent (%)</option>
                                            <option value="fixed"   @selected($typeOld === 'fixed')>Fixed amount</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Discount Value</label>
                                        <input type="number" step="0.01" min="0.01" name="discount_value" class="form-control"
                                               value="{{ old('discount_value', $discountCoupon->discount_value) }}" required>
                                        <small class="text-secondary">If type is percent, max 100.</small>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Minimum Amount</label>
                                        <input type="number" step="0.01" min="0" name="min_amount" class="form-control"
                                               value="{{ old('min_amount', $discountCoupon->min_amount) }}" placeholder="Optional">
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Max Uses</label>
                                        <input type="number" step="1" min="1" name="max_uses" class="form-control"
                                               value="{{ old('max_uses', $discountCoupon->max_uses) }}" placeholder="Leave empty for unlimited">
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Status</label>
                                        <select name="status" class="form-control form-select" required>
                                            <option value="1" {{ $discountCoupon->status === '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $discountCoupon->status === '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Update</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
