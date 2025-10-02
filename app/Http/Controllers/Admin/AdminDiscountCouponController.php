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
        
        notyf()->success('Coupon created successfully!');
        return to_route('admin.discount-coupon.index');
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

        notyf()->success('Coupon updated successfully!');
        return to_route('admin.discount-coupon.index');
    }

    public function destroy(string $id)
    {

        try {
            $discountCoupon = DiscountCoupon::findOrFail($id);
            $discountCoupon->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }

    }

    public function generateCode(User $agent)
    {
        return response()->json([
            'code' => generateUniqueDiscountCouponCode(),
        ]);
    }

}
