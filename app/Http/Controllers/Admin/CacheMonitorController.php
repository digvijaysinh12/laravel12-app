<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Cache\CacheMonitorService;
use Illuminate\Support\Facades\Cache;

class CacheMonitorController extends Controller
{
    public function __construct(private CacheMonitorService $monitor)
    {
    }

    public function index()
    {
        $stats = $this->monitor->stats();
        return view('admin.cache.index', compact('stats'));
    }

    public function clearAll()
    {
        $this->monitor->flushAll();
        return back()->with('success', 'Cache cleared');
    }

    public function clearTag(string $tag)
    {
        $this->monitor->flushTag($tag);
        return back()->with('success', "Cache tag '{$tag}' cleared");
    }
}
