<?php

namespace App\Http\Controllers\Frontend\Agent;

use App\DataTables\AgentDiscountCouponsDataTable;
use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;

class AgentDiscountCouponController extends Controller
{

    public function index(AgentDiscountCouponsDataTable $dataTable)
    {
        $discountCoupons = DiscountCoupon::with(['agent:id,name,email'])->latest()->paginate(30);
        return $dataTable->render('frontend.agent.discount-coupon.index', compact('discountCoupons'));
    }

}
