<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Badge extends Component
{
    public function __construct(
        public string $tone = 'neutral',
    ) {
    }

    public function render(): View
    {
        return view('admin.components.badge');
    }
}
