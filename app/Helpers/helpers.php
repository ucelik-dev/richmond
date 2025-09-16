<?php

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