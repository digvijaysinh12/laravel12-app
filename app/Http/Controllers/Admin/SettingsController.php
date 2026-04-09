<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function update()
    {
        return back()->with('success', 'Settings saved successfully.');
    }
}
