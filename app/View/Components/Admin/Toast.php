<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Toast extends Component
{
    public function __construct(
        public string $tone = 'info',
        public ?string $message = null,
    ) {
    }

    public function render(): View
    {
        return view('admin.components.toast');
    }
}
