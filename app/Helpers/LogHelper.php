<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Context;

class LogHelper
{
    public static function log(string $message, string $level = 'info', array $context = []): void
    {
        $level = self::normalizeLevel($level);
        $userType = Context::get('user_type', 'guest');
        $payload = array_filter(
            array_merge(Context::all(), $context),
            static fn ($value) => $value !== null
        );

        if ($userType === 'admin') {
            Log::channel('admin')->{$level}($message, $payload);
            return;
        }

        Log::channel('customer')->{$level}($message, $payload);
    }

    private static function normalizeLevel(string $level): string
    {
        return in_array($level, [
            'debug',
            'info',
            'notice',
            'warning',
            'error',
            'critical',
            'alert',
            'emergency',
        ], true) ? $level : 'info';
    }
}
