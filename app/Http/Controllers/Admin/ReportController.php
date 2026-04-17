<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function download($file)
    {
        if (!Storage::disk('reports')->exists($file)) {
            abort(404);
        }

        return Storage::disk('reports')->download($file);
    }
}
