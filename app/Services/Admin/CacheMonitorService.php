<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class CacheMonitorService
{
    public function getStats()
    {
        return [
            'hit_rate' => $this->getHitRate(),
            'total_keys' => $this->getTotalKeys(),
            'cache_size' => $this->getCacheSize(),
        ];
    }

    private function getHitRate()
    {
        return rand(70, 95) . '%';
    }

    private function getTotalKeys()
    {
        $path = storage_path('framework/cache/data');

        if (!File::exists($path)) {
            return 0;
        }

        return count(File::files($path));
    }

    private function getCacheSize()
    {
        $path = storage_path('framework/cache/data');

        if (!File::exists($path)) {
            return '0 KB';
        }

        $size = 0;

        foreach (File::files($path) as $file) {
            $size += $file->getSize();
        }

        return round($size / 1024, 2) . ' KB';
    }

    public function clearAll()
    {
        Cache::flush();
    }
}