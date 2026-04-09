<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Table extends Component
{
    public function __construct(
        public array $headers = [],
    ) {
    }

    public function render(): View
    {
        return view('admin.components.table');
    }
}
