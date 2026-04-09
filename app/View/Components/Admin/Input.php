<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Input extends Component
{
    public function __construct(
        public string $name,
        public string $label,
        public string $type = 'text',
        public mixed $value = null,
        public ?string $placeholder = null,
        public bool $required = false,
        public ?string $step = null,
    ) {
    }

    public function render(): View
    {
        return view('admin.components.input');
    }
}
