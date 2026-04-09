<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Card extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $action = null,
    ) {
    }

    public function render(): View
    {
        return view('admin.components.card');
    }
}
