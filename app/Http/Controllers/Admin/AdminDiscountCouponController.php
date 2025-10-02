<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\DiscountCouponDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminDiscountCouponCreateRequest;
use App\Http\Requests\Admin\AdminDiscountCouponUpdateRequest;
use App\Models\DiscountCoupon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminDiscountCouponController extends Controller
{

    public function index(DiscountCouponDataTable $dataTable)
    {
        $discountCoupons = DiscountCoupon::with(['agent:id,name,email'])->latest()->paginate(30);
        return $dataTable->render('admin.discount-coupon.index', compact('discountCoupons'));
    }

    public function create()
    {
        $agents = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'agent'))
            ->orderBy('company')
            ->get(['id','name','company']);

        return view('admin.discount-coupon.create', compact('agents'));
    }

    public function store(AdminDiscountCouponCreateRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::user()->id;

        DiscountCoupon::create($data);

        return redirect()
            ->route('admin.discount-coupon.index')
            ->with('success', 'Discount coupon created.');
    }

    public function show(string $id)
    {
        $discountCoupon = DiscountCoupon::with(['agent:id,name,email','creator:id,name,email','usages.student:id,name,email'])
            ->findOrFail($id);

        return view('admin.discount-coupon.show', compact('discountCoupon'));
    }

    public function edit(string $id)
    {
        $discountCoupon = DiscountCoupon::findOrFail($id);

        $agents = User::whereHas('roles', fn($q) => $q->where('name','agent'))
            ->orderBy('name')
            ->get(['id','name','email']);

        return view('admin.discount-coupon.edit', compact('discountCoupon','agents'));
    }

    public function update(AdminDiscountCouponUpdateRequest $request, DiscountCoupon $coupon)
    {
        $coupon->update($request->validated());
        return back()->with('success', 'Coupon updated.');
    }

    public function destroy(string $id)
    {
        $discountCoupon = DiscountCoupon::findOrFail($id);
        $discountCoupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    public function generateCode(User $agent)
    {
        return response()->json([
            'code' => generateUniqueDiscountCouponCode(),
        ]);
    }

}
