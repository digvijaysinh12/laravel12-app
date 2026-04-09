<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Button extends Component
{
    public function __construct(
        public string $variant = 'primary',
        public ?string $href = null,
        public string $type = 'button',
    ) {
    }

    public function render(): View
    {
        return view('admin.components.button');
    }
}
