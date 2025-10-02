<?php

use App\Models\DiscountCoupon;

if (!function_exists('currency_format')) {
    function currency_format($amount, $symbol = '£'): string
    {
        return $symbol . number_format((float) $amount, 2, '.', ',');
    }
}

if (!function_exists('getFileIcon')) {
    function getFileIcon($extension)
    {
        $extension = strtolower($extension);

        return match (true) {
            in_array($extension, ['doc', 'docx']) => 'fa-file-word',
            in_array($extension, ['xls', 'xlsx']) => 'fa-file-excel',
            in_array($extension, ['ppt', 'pptx']) => 'fa-file-powerpoint',
            in_array($extension, ['zip', 'rar']) => 'fa-file-archive',
            in_array($extension, ['pdf']) => 'fa-file-pdf',

            default => 'fa-file-lines',
        };
    }
}

/* Set sidebar active */
if(!function_exists('setSidebarActive')){
    function setSidebarActive(array $routes) 
    {
        foreach($routes as $route){
            if(request()->routeIs($route)){
                return 'show fw-bold';
            }
        }
        return '';
    }
}

/* Set sidebar active */
if(!function_exists('setFrontendSidebarActive')){
    function setFrontendSidebarActive(array $routes) 
    {
        foreach($routes as $route){
            if(request()->routeIs($route)){
                return 'active';
            }
        }
        return '';
    }
}

if (!function_exists('generateDiscountCouponCode')) {
    function generateDiscountCouponCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789*%$#@&'; // no 0,O,1,I
        $len = strlen($alphabet);
        $bytes = random_bytes(8);
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $alphabet[ord($bytes[$i]) % $len];
        }
        return $code;
    }
}

if (!function_exists('generateUniqueDiscountCouponCode')) {
    function generateUniqueDiscountCouponCode(int $maxAttempts = 8): string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = generateDiscountCouponCode();
            if (!DiscountCoupon::where('code', $code)->exists()) return $code;
        }
        throw new RuntimeException('Failed to generate a unique coupon code.');
    }
}