<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Sidebar extends Component
{
    public function render(): View
    {
        return view('admin.components.sidebar');
    }
}
