<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CacheMonitorService;
use Illuminate\Support\Facades\Cache;

class CacheMonitorController extends Controller
{
    protected $service;

    public function __construct(CacheMonitorService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $stats = $this->service->getStats();

        return view('admin.cache.index', compact('stats'));
    }

    public function clear()
    {
        $this->service->clearAll();

        return back()->with('success', 'Cache cleared successfully');
    }

    public function clearTag(string $tag)
    {
        try {
            Cache::tags($tag)->flush();
        } catch (\Exception $e) {
            Cache::flush(); // fallback for file cache
        }

        return back()->with('success', "Cache tag '{$tag}' cleared");
    }
}