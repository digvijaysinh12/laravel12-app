<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Select extends Component
{
    public function __construct(
        public string $name,
        public string $label,
        public array $options = [],
        public mixed $selected = null,
        public ?string $placeholder = 'Select one',
        public bool $required = false,
    ) {
    }

    public function render(): View
    {
        return view('admin.components.select');
    }
}
