<?php

if (!function_exists('format_price')) {
    function format_price($amount) {
        return $amount;
    }
}

if (!function_exists('order_status_badge')) {
    function order_status_badge($status) {
        return match ($status) {
            'pending' => 'warning',
            'paid' => 'success',
            default => 'secondary',
        };
    }
}